<?php
namespace App\Context;

/**
 * 審査例外情報一覧を表現します。
 */
class Warns
{
    private $list = [];

    private function __construct()
    {}

    public function add(string $message, $args = []): Warns
    {
        return $this->addField("", $message, $args);
    }

    public function addField(string $field, string $message, $args = []): Warns
    {
        array_push($this->list, Warn::ofField($field, $message, $args));
        return $this;
    }

    public function head(): string
    {
        return $this->nonEmpty() ? $this->list[0]->message : "";
    }

    public function values(): array
    {
        return $this->list;
    }

    public function nonEmpty(): bool
    {
        return !empty($this->list);
    }

    public static function init(string $message = null): Warns
    {
        $warns = new Warns();
        if (isset($message)) {
            $warns->add($message);
        }
        return $warns;
    }

}
