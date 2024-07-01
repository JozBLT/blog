<?php

namespace Framework;

use Framework\Validator\ValidationError;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
    private const MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];
    private array $params;
    /**
     * @var string[]
     */
    private array $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);

        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            $length < $min
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            $length > $max
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }

        return $this;
    }

    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';

        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }

        return $this;
    }

    public function dateTime(string $key, string $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();

        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }

        return $this;
    }

    /** Check if key exists in repository */
    public function exists(string $key, string $repository, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $repository WHERE id = ?");
        $statement->execute([$value]);

        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$repository]);
        }

        return $this;
    }

    /** Check if key is unique */
    public function unique(string $key, string $repository, \PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM $repository WHERE $key = ?";
        $params = [$value];

        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }

        $statement = $pdo->prepare($query);
        $statement->execute($params);

        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }

        return $this;
    }

    /** Check if file has been uploaded */
    public function uploaded(string $key): self
    {
        $file = $this->getValue($key);

        if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }

        return $this;
    }

    /** Check file's format */
    public function extension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);

        if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MIME_TYPES[$extension] ?? null;

            if (!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'fileType', [join(',', $extensions)]);
            }
        }

        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /** @return ValidationError[] */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    /** @return mixed|null */
    private function getValue(string $key): mixed
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return null;
    }
}
