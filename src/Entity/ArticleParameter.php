<?php

namespace flexycms\FlexyArticlesBundle\Entity;


class ArticleParameter
{

    private $code;
    private $label;
    private $type;

    private $value = '';

    /**
     * ArticleParameter constructor.
     * @param string|array $code
     * @param ?string $label
     * @param ?string $type
     */
    public function __construct($code = null, $label = null, $type = null)
    {
        if ($code !== null && $label === null && $type === null)  // Передан массив
        {
            $this->code = $code['code'];
            $this->label = $code['label'];
            $this->type = $code['type'];
            $this->value = isset($code['value'])?$code['value']:$this->getDefault($this->type);;

        }
        elseif($code == null) {     //Не передан ни один параметр
            $this->code = "Новый параметр";
        }
        else {  // Переданы все параметры
            $this->code = $code;
            $this->label = $label;
            $this->type = $type;
            $this->value = $this->getDefault($this->type);
        }
    }


    public function getDefault($type)
    {
        if ($type == 'string') return '';
        if ($type == 'int') return 0;
        if ($type == 'float') return 0.0;
        if ($type == 'datetime') return new \DateTime();
        if ($type == 'bool') return true;
        return '';
    }


    /**
     * Возвращает массив возможных типов параметра
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
             'строка' => 'string',
             'целое число' => 'int',
             'дробное число' => 'float',
             'дата и время' => 'datetime',
             'флажок' => 'bool',
        ];
    }



    public function toArray()
    {
        $result = array();

        $result['code'] = $this->code;
        $result['label'] = $this->label;
        $result['type'] = $this->type;
        $result['value'] = $this->value;

        return $result;
    }


    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed|string
     */
    public function getValue()
    {
        if ($this->type == 'datetime' && is_array($this->value)) return new \DateTime($this->value['date']);
        return $this->value;
    }

    /**
     * @param mixed|string $value
     */
    public function setValue($value): self
    {
        if ($this->type == 'datetime')
        {
            $this->value = $value;
        }
        else
        {
            $this->value = $value;
        }

        return $this;
    }


}