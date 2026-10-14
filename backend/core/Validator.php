<?php
declare(strict_types=1);

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = null): self
    {
        if (!isset($this->data[$field]) || trim((string)$this->data[$field]) === '') {
            $this->errors[$field] = ($label ?? $field) . ' é obrigatório.';
        }
        return $this;
    }

    public function email(string $field): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'E-mail inválido.';
        }
        return $this;
    }

    public function min(string $field, int $min): self
    {
        if (!empty($this->data[$field]) && mb_strlen((string)$this->data[$field]) < $min) {
            $this->errors[$field] = "Mínimo {$min} caracteres.";
        }
        return $this;
    }

    public function max(string $field, int $max): self
    {
        if (!empty($this->data[$field]) && mb_strlen((string)$this->data[$field]) > $max) {
            $this->errors[$field] = "Máximo {$max} caracteres.";
        }
        return $this;
    }

    public function numeric(string $field): self
    {
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = 'Deve ser numérico.';
        }
        return $this;
    }

    public function in(string $field, array $allowed): self
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[$field] = 'Valor inválido.';
        }
        return $this;
    }

    public function unique(string $field, string $table, string $column = null): self
    {
        $column = $column ?? $field;
        if (empty($this->data[$field])) return $this;

        $exists = Database::fetch("SELECT id FROM {$table} WHERE {$column} = ? LIMIT 1", [$this->data[$field]]);
        if ($exists) $this->errors[$field] = 'Este valor já está em uso.';
        return $this;
    }

    public function fails(): bool { return !empty($this->errors); }
    public function errors(): array { return $this->errors; }

    public function validate(): array
    {
        if ($this->fails()) {
            Response::error('Erro de validação.', 422, ['fields' => $this->errors]);
        }
        return $this->data;
    }
}