<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    Class Validation_form
    {
        protected $rules = [];
        protected $errors =[];

        public function setRules(array $rules)
        {
            $this->rules = $rules;
        }

        public function validate(array $data): bool
        {
            $this->errors = [];

            foreach ($this->rules as $field => $rules)
            {
                $value = isset($data[$field]) ? trim($data[$field]) : null;

                foreach ($rules as $rule)
                {
                    $param = null;

                    if(strpos($rule, ':') !== false)
                    {
                        [$rule, $param] = explode(':', $rule, 2);
                    }

                    switch($rule)
                    {
                        case 'required':
                            if($value === '' || $value === null)
                            {
                                $this->addError($field, 'El campo es obligatorio');
                            }
                            break;

                        case 'numeric':
                            if(!is_numeric($value))
                            {
                                $this->addError($field, 'Debe ser un valor numérico');
                            }
                            break;
                        
                        case 'integer':
                            if(filter_var($value, FILTER_VALIDATE_INT) === false)
                            {
                                $this->addError($field, 'Debe ser un número entero');
                            }
                            break;

                        case 'min':
                            if(is_numeric($value) && $value < $param)
                            {
                                $this->addError($field, "Debe ser mayor o igual a {$param}");
                            }
                            break;
                        
                        case 'valid_date':
                            if($value === '0000-00-00 00:00:00' || strtotime($value)=== false)
                            {
                                $this->addError($field, 'La fecha no es válida');
                            }
                            break;
                        
                        case 'max_today':
                            if(!empty($value))
                            {
                                $input_date = strtotime(date('Y-m-d', strtotime($value)));
                                $today = strtotime(date('Y-m-d'));
                                if($input_date > $today)
                                {
                                    $this->addError($field, 'La fecha no puede ser mayor al día de hoy');
                                }
                            }
                            break;
                        case 'email':
                            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL))
                            {
                                $this->addError($field, 'El correo electrónico no es válido');
                            }
                            break;
                        case 'alpha_numeric':
                            if (!empty($value) && !preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
                                $this->addError($field, 'Solo se permiten letras y números');
                            }
                            break;
                        case 'unidad_mayor':
                            if ($value !== null && $value !== '') {

                                // Buscar cualquier número en el texto
                                if (preg_match('/(\d+)/', $value, $matches)) {

                                    $number = intval($matches[1]);

                                    // Validar que sea mayor que 0
                                    if ($number <= 0) {
                                        $this->addError($field, 'El valor numérico debe ser mayor que 0');
                                    }

                                } else {
                                    // No se encontró número en el texto
                                    $this->addError($field, 'Debe contener un número mayor que 0');
                                }
                            }
                            break;
                        case 'decimal':
                            if ($value !== null && $value !== '') {
                                if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                                    $this->addError($field, 'Debe ser un número válido con hasta 2 decimales');
                                }
                            }
                            break;

                        default:
                            break;
                    }
                }
            }
            return empty($this->errors);
        }

        protected function addError($field, $message)
        {
            $this->errors[$field][] = $message;
        }

        public function getErrors(): array
        {
            return $this->errors;
        }
    }


?>