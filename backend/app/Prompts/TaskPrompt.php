<?php

namespace App\Prompts;

class TaskPrompt
{
    /**
     * Get task structure.
     */
    public static function getTaskStructure(): string
    {
        return <<<'TEXT'
      Each task should have the following structure:
    [
      [
        "title" => "Task Title",
        "status" => "BACKLOG" | "TO_DO" | "IN_PROGRESS" | "DONE" | "CANCELED",
        "priority" => "LOW" | "MEDIUM" | "HIGH",
        "label" => "INVENTORY" | "ORDER" | "SHIPPING" | "QUALITY" | "CUSTOMER" | "MAINTENANCE",
        "due_date" => "2024-11-23T12:00:00",
      }
    ]
    TEXT;
    }

    /**
     * Get example output.
     */
    public static function getExampleOutput(): string
    {
        return <<<'TEXT'
        Here is an example of the expected output:
    [
      {
        "title" => "Restock inventory",
        "status" => "TO_DO",
        "priority" => "HIGH",
        "label" => "INVENTORY",
        "due_date "=> "2024-11-30T09:00:00",
      },
      {
        "title" => "Ship order #1234",
        "status" => "IN_PROGRESS",
        "priority" => "MEDIUM",
        "label" => "SHIPPING",
        "due_date" => "2024-11-25T12:00:00",
      },
    ]
    TEXT;
    }
}
