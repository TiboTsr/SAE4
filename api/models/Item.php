<?php

namespace model;

use JsonSerializable;

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/File.php';

class Item extends BaseModel implements JsonSerializable
{
    private static function selectClause(): string
    {
        return "id_article, nom_article, xp_article, stock_article, image_article, reduction_article, prix_article,
                '' AS categorie_article, deleted";
    }

    public static function create(string $name, int $xp, int $stocks, bool $reduction, float $price, File | null $image, string $categorie_article): Item
    {
        $DB = new \DB();
        $imageFileName = $image?->getFileName() ?? 'N/A';

        $id = $DB->query("INSERT INTO ARTICLE (nom_article, xp_article, stock_article, reduction_article, prix_article, image_article)
                    VALUES (?, ?, ?, ?, ?, ?)", "siiids", [$name, $xp, $stocks, (int) $reduction, $price, $imageFileName]);
        return new Item($id);
    }

    public function update(string $name, int $xp, int $stocks, bool $reduction, float $price, string $categorie_article): Item
    {
        $this->DB->query("UPDATE ARTICLE SET nom_article = ?, xp_article = ?, stock_article = ?, reduction_article = ?, prix_article = ? WHERE id_article = ?", "siiidi", [$name, $xp, $stocks, (int) $reduction, $price, $this->id]);

        return $this;
    }

    public function getImage(): File | null
    {
        $image = $this->DB->select("SELECT image_article FROM ARTICLE WHERE id_article = ?", "i", [$this->id])[0]['image_article'];
        return File::getFile($image);
    }

    public function updateImage(File $image): Item
    {
        $this->DB->query("UPDATE ARTICLE SET image_article = ? WHERE id_article = ?", "si", [$image->getFileName(), $this->id]);

        return $this;
    }

    public function delete(): void
    {
        $this->getImage()?->deleteFile();
        $this->DB->query("UPDATE ARTICLE SET deleted=true WHERE id_article = ?", "i", [$this->id]);
    }

    public static function getInstance($id): ?Item
    {
        $DB = new \DB();
        $result = $DB->select("SELECT * FROM ARTICLE WHERE id_article = ? AND DELETED = FALSE", "i", [$id]);

        if (count($result) == 0) {
            return null;
        }

        return new Item($id);
    }

    public function jsonSerialize(): array
    {
        return $this->DB->select("SELECT " . self::selectClause() . " FROM ARTICLE WHERE id_article = ?", "i", [$this->id])[0];
    }

    public static function bulkFetch(): array
    {
        $DB = new \DB();
        return $DB->select("SELECT " . self::selectClause() . " FROM ARTICLE WHERE DELETED = FALSE");
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}
