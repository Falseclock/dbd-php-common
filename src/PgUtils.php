<?php

namespace Falseclock\DBD\Common;

use DBD\DBD;
use Falseclock\DBD\Entity\Column;
use Falseclock\DBD\Entity\Constraint;
use Falseclock\DBD\Entity\Primitive;
use Falseclock\DBD\Entity\Table;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;

class PgUtils implements DBDUtils
{
	/** @var DBD $db */
	private $db;

	/**
	 * PgUtils constructor.
	 *
	 * @param DBD $dbDriver
	 */
	public function __construct(DBD $dbDriver) {
		$this->db = $dbDriver;
	}

	/**
	 * @param string $type
	 *
	 * @return Primitive
	 * @throws DBDException
	 */
	public function getPrimitive(string $type) {
		switch(strtolower(trim($type))) {

			case 'bytea':
				return Primitive::Binary();
				break;

			case 'boolean':
			case 'bool':
				return Primitive::Boolean();
				break;

			case 'date':
			case 'timestamp':
				return Primitive::Date();
				break;

			case 'timestamptz':
				return Primitive::DateTimeOffset();
				break;

			case 'numeric':
			case 'decimal':
				return Primitive::Decimal();
				break;

			case 'float8':
				return Primitive::Double();
				break;

			case 'interval':
				return Primitive::Duration();
				break;

			case 'uuid':
				return Primitive::Guid();
				break;

			case 'int2':
			case 'smallint':
			case 'smallserial':
			case 'serial2':
				return Primitive::Int16();
				break;

			case 'int':
			case 'int4':
			case 'integer':
			case 'serial4':
			case 'serial':
				return Primitive::Int32();
				break;

			case 'int8':
			case 'bigint':
			case 'bigserial':
			case 'serial8':
				return Primitive::Int64();
				break;

			case 'float4':
			case 'real':
				return Primitive::Single();
				break;

			case 'varchar':
			case 'text':
			case 'cidr':
			case 'inet':
			case 'json':
			case 'jsonb':
			case 'macaddr':
			case 'macaddr8':
			case 'char':
			case 'tsquery':
			case 'tsvector':
			case 'xml':
				return Primitive::String();
				break;
		}

		throw new DBDException("Not described type found: {$type}");
	}

	/**
	 * @param Table $table
	 *
	 * @return Constraint[]
	 * @throws DBDException
	 * @throws InvalidArgumentException
	 * @throws ReflectionException
	 */
	public function getTableConstraints(Table $table) {
		$constraints = [];
		$sth = $this->db->prepare("
			SELECT
				kcu.column_name,
				ccu.table_schema AS foreign_table_schema,
				ccu.table_name   AS foreign_table_name,
				ccu.column_name  AS foreign_column_name
			FROM
				information_schema.table_constraints AS tc
				JOIN information_schema.key_column_usage AS kcu
					 ON tc.constraint_name = kcu.constraint_name
						 AND tc.table_schema = kcu.table_schema
				JOIN information_schema.constraint_column_usage AS ccu
					 ON ccu.constraint_name = tc.constraint_name
						 AND ccu.table_schema = tc.table_schema
			WHERE
				tc.constraint_type = 'FOREIGN KEY' AND
				tc.table_name = ? AND
				tc.table_schema = ?
		"
		);
		$sth->execute($table->name, $table->scheme);

		if($sth->rows()) {
			while($row = $sth->fetchRow()) {
				$constraint = new Constraint();
				$constraint->column = $this->getColumnByName($table->columns, $row['column_name']);
				$constraint->foreignTable = $this->tableStructure($row['foreign_table_name'], $row['foreign_table_schema']);
				$constraint->foreignColumn = $this->getColumnByName($constraint->foreignTable->columns, $row['foreign_column_name']);

				$constraints[] = $constraint;
			}
		}

		return $constraints;
	}

	/**
	 * @param string $tableName
	 * @param string $schemaName
	 *
	 * @return Table
	 * @throws DBDException
	 * @throws InvalidArgumentException
	 * @throws ReflectionException
	 */
	public function tableStructure(string $tableName, string $schemaName) {

		$table = new Table();
		$table->name = $tableName;
		$table->scheme = $schemaName;

		$table->annotation = $this->db->select("SELECT obj_description(CONCAT(?::text, '.', ?::text)::REGCLASS)", $table->scheme, $table->name);

		$sth = $this->db->prepare("
			SELECT
				CASE WHEN ordinal_position = ANY (i.indkey) THEN TRUE ELSE FALSE END                     AS is_primary,
				ordinal_position,
				cols.column_name,
				CASE WHEN is_nullable = 'NO' THEN FALSE WHEN is_nullable = 'YES' THEN TRUE ELSE NULL END AS is_nullable,
				data_type,
				udt_name,
				character_maximum_length,
				numeric_precision,
				numeric_scale,
				datetime_precision,
				column_default,
				pg_catalog.col_description(CONCAT(cols.table_schema, '.', cols.table_name)::REGCLASS::OID, cols.ordinal_position::INT) AS column_comment
			FROM
				information_schema.columns cols
				LEFT JOIN pg_index i ON i.indrelid = CONCAT(cols.table_schema, '.', cols.table_name)::REGCLASS::OID AND i.indisprimary
			WHERE
				cols.table_name = ? AND
				cols.table_schema = ?
			ORDER BY
				ordinal_position
		"
		);
		$sth->execute($table->name, $table->scheme);

		if($sth->rows()) {
			$columns = [];
			while($row = $sth->fetchRow()) {
				$column = new Column();
				$column->name = $row['column_name'];

				if(isset($row['is_nullable'])) {
					if($row['is_nullable'] == 'f' || $row['is_nullable'] == false)
						$column->nullable = false;
					else
						$column->nullable = false;
				}

				if(isset($row['character_maximum_length']))
					$column->maxLength = $row['character_maximum_length'];

				if(isset($row['numeric_precision']))
					$column->precision = $row['numeric_precision'];

				if(isset($row['numeric_scale']))
					$column->scale = $row['numeric_scale'];

				if(isset($row['datetime_precision']))
					$column->precision = $row['datetime_precision'];

				if(isset($row['column_default']))
					$column->defaultValue = $row['column_default'];

				if(isset($row['column_comment']))
					$column->annotation = $row['column_comment'];

				$column->type = self::getPrimitive($row['udt_name']);

				if(in_array($column->type->getValue(), [ Primitive::Int16, Primitive::Int32(), Primitive::Int64 ])) {
					$column->scale = null;
					$column->precision = null;
				}

				if(isset($row['is_primary'])) {
					if($row['is_primary'] === 'f' or $row['is_primary'] === false) {
						$column->key = false;
					}
					else {
						$column->key = true;
						$table->keys[] = $column;
					}
				}

				$columns[] = $column;
			}

			$table->columns = $columns;

			$table->constraints = $this->getTableConstraints($table);
		}

		return $table;
	}

	/**
	 * @param Column[] $columns
	 * @param          $name
	 *
	 * @return Column
	 * @throws DBDException
	 */
	private function getColumnByName(iterable $columns, $name): Column {
		foreach($columns as $column) {
			if($column->name == $name) {
				return $column;
			}
		}
		throw  new DBDException("Unknown column {$name}");
	}
}