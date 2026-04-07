<?php

namespace model;

use JsonSerializable;

require_once __DIR__ . '/File.php';
require_once __DIR__ . '/BaseModel.php';

class Event extends BaseModel implements JsonSerializable
{
    private static function hasColumn(string $columnName): bool
    {
        static $cache = [];

        if (array_key_exists($columnName, $cache)) {
            return $cache[$columnName];
        }

        $DB = new \DB();
        $result = $DB->select(
            "SELECT 1
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'EVENEMENT'
               AND COLUMN_NAME = ?
             LIMIT 1",
            "s",
            [$columnName]
        );
        $cache[$columnName] = !empty($result);

        return $cache[$columnName];
    }

    private static function selectClause(): string
    {
        $imageSelect = self::hasColumn('image_evenement') ? "image_evenement" : "NULL AS image_evenement";
        $descriptionSelect = self::hasColumn('description_evenement')
            ? "COALESCE(description_evenement, '') AS description_evenement"
            : "'' AS description_evenement";

        return "id_evenement, nom_evenement, xp_evenement, places_evenement, prix_evenement, reductions_evenement, lieu_evenement, date_evenement,
                {$imageSelect}, {$descriptionSelect}, deleted";
    }

    public function delete() : void
    {
        $this->DB->query("UPDATE EVENEMENT SET deleted=true WHERE id_evenement = ?", "i", [$this->id]);
    }

    public function update(string $nom, string $description, int $xp, int $places, bool $reductions, float $prix, string $lieu, string $date) : Event
    {
        $fields = ["nom_evenement = ?"];
        $types = "s";
        $values = [$nom];

        if (self::hasColumn('description_evenement')) {
            $fields[] = "description_evenement = ?";
            $types .= "s";
            $values[] = $description;
        }

        $fields[] = "xp_evenement = ?";
        $types .= "i";
        $values[] = $xp;

        $fields[] = "places_evenement = ?";
        $types .= "i";
        $values[] = $places;

        $fields[] = "reductions_evenement = ?";
        $types .= "i";
        $values[] = (int) $reductions;

        $fields[] = "prix_evenement = ?";
        $types .= "d";
        $values[] = $prix;

        $fields[] = "lieu_evenement = ?";
        $types .= "s";
        $values[] = $lieu;

        $fields[] = "date_evenement = ?";
        $types .= "s";
        $values[] = $date;

        $types .= "i";
        $values[] = $this->id;

        $this->DB->query(
            "UPDATE EVENEMENT SET " . implode(", ", $fields) . " WHERE id_evenement = ?",
            $types,
            $values
        );

        return $this;
    }

    public function getImage() : File | null
    {
        if (!self::hasColumn('image_evenement')) {
            return null;
        }

        $row = $this->DB->select("SELECT image_evenement FROM EVENEMENT WHERE id_evenement = ?", "i", [$this->id]);
        $imageName = $row[0]['image_evenement'] ?? null;

        return File::getFile($imageName);
    }

    public function updateImage(File $image) : Event
    {
        if (!self::hasColumn('image_evenement')) {
            return $this;
        }

        $oldImage = $this->getImage();
        $this->DB->query("UPDATE EVENEMENT SET image_evenement = ? WHERE id_evenement = ?", "si", [(string) $image, $this->id]);

        if ($oldImage !== null) {
            $oldImage->deleteFile();
        }

        return $this;
    }

    public static function getInstance(int $id): ?Event
    {
        $DB = new \DB();
        $sql = "SELECT * FROM EVENEMENT WHERE id_evenement = ? AND deleted=false";
        $event = $DB->select($sql, "i", [$id]);

        if (count($event) == 0) {
            return null;
        }

        return new Event($id);
    }

    public static function create(string $nom, string $description, int $xp, int $places, bool $reductions, float $prix, string $lieu, string $date) : Event
    {
        $DB = new \DB();
        $columns = ["nom_evenement"];
        $placeholders = ["?"];
        $types = "s";
        $values = [$nom];

        if (self::hasColumn('description_evenement')) {
            $columns[] = "description_evenement";
            $placeholders[] = "?";
            $types .= "s";
            $values[] = $description;
        }

        if (self::hasColumn('image_evenement')) {
            $columns[] = "image_evenement";
            $placeholders[] = "NULL";
        }

        $columns = array_merge($columns, ["xp_evenement", "places_evenement", "reductions_evenement", "prix_evenement", "lieu_evenement", "date_evenement"]);
        $placeholders = array_merge($placeholders, ["?", "?", "?", "?", "?", "?"]);
        $types .= "iiidss";
        $values = array_merge($values, [$xp, $places, (int) $reductions, $prix, $lieu, $date]);

        $id = $DB->query(
            "INSERT INTO EVENEMENT (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")",
            $types,
            $values
        );

        return new Event($id);
    }


    public static function bulkFetch() : array
    {
        $DB = new \DB();
        $sql = "SELECT " . self::selectClause() . " FROM EVENEMENT WHERE deleted=false";
        return $DB->select($sql);
    }

    public function jsonSerialize(): array
    {
        return $this->DB->select("SELECT " . self::selectClause() . " FROM EVENEMENT WHERE id_evenement = ?", "i", [$this->id])[0];

    }

    public function __toString() : string
    {
        return json_encode($this);
    }
}
