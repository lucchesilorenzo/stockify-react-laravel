<?php

namespace App\Helpers;

use Illuminate\Support\Str;


class StringHelper
{
  /**
   * Generate a SKU based on the category name and product name.
   *
   * @param string $categoryName
   * @param string $productName
   * @return string
   */
  public static function generateSKU(string $categoryName, string $productName): string
  {
    $categoryPart = strtoupper(substr($categoryName, 0, 3));
    $namePart = strtoupper(substr($productName, 0, 3));
    $idPart = strtoupper(substr(Str::uuid()->toString(), -4));

    return "{$categoryPart}-{$namePart}-{$idPart}";
  }

  /**
   * Generate a slug based on the given text.
   *
   * @param string $text
   * @return string
   */
  public static function generateSlug(string $text): string
  {
    return Str::slug($text, '-');
  }
}
