<?php
/**
 * Validator.php — минимальный набор серверных проверок.
 * Клиентская валидация (HTML5 + JS) ускоряет обратную связь, но не заменяет
 * эту серверную проверку: клиенту никогда нельзя доверять.
 */
class Validator
{
    private array $errors = [];

    public function required(array $data, string $field, string $label): self
    {
        if (trim((string)($data[$field] ?? '')) === '') {
            $this->errors[$field] = "Поле «$label» обязательно для заполнения";
        }
        return $this;
    }

    public function email(array $data, string $field = 'email'): self
    {
        $value = trim((string)($data[$field] ?? ''));
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Некорректный e-mail';
        }
        return $this;
    }

    public function minLength(array $data, string $field, int $min, string $label): self
    {
        $value = (string)($data[$field] ?? '');
        if (mb_strlen($value) < $min) {
            $this->errors[$field] = "«$label» должно содержать не менее $min символов";
        }
        return $this;
    }

    public function phone(array $data, string $field = 'phone'): self
    {
        $value = trim((string)($data[$field] ?? ''));
        if ($value !== '' && !preg_match('/^[0-9+()\-\s]{6,20}$/', $value)) {
            $this->errors[$field] = 'Некорректный номер телефона';
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
