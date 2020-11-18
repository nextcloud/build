<?php

declare(strict_types=1);

namespace OCA\Build\Migration;

use Closure;
use Doctrine\DBAL\Types\Types;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version010000Date20201102190317 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('build_apps')) {
			$table = $schema->createTable('build_apps');
			$table->addColumn('id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 128, // following appinfo name length
			]);
			$table->addColumn('description', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('version', Types::STRING, [
				'notnull' => true,
				'length' => 256, // following apps' scheme
				'default' => '1.0.0'
			]);
			$table->addColumn('created', Types::INTEGER, [
				'notnull' => false,
				'comment' => 'unix-timestamp',
			]);
			$table->addColumn('last_modified', Types::INTEGER, [
				'notnull' => false,
				'comment' => 'unix-timestamp',
			]);

			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('build_tables')) {
			$table = $schema->createTable('build_tables');
			$table->addColumn('id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('app_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 128,
			]);

			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('build_column_definitions')) {
			$table = $schema->createTable('build_column_definitions');
			$table->addColumn('id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('app_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('table_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 128,
			]);
			$table->addColumn('datatype', Types::STRING, [
				'notnull' => true,
				'length' => 256,
			]);
			$table->addColumn('mandatory', Types::BOOLEAN, [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn('default', Types::TEXT, [
				'notnull' => false,
			]);

			$table->setPrimaryKey(['id'], 'id_coldefs');
		}

		if (!$schema->hasTable('build_rows')) {
			$table = $schema->createTable('build_rows');
			$table->addColumn('id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('table_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['id', 'table_id'], 'idx_tbl_row');
		}

		if (!$schema->hasTable('build_values')) {
			$table = $schema->createTable('build_column_values');
			$table->addColumn('id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('row_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('col_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('val', Types::TEXT, [
				'notnull' => false,
			]);

			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('build_options')) {
			$table = $schema->createTable('build_options');
			$table->addColumn('id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('col_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('val', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('order', Types::INTEGER, [
				'notnull' => true,
				'default' => 1,
			]);

			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('build_view_configuration')) {
			$table = $schema->createTable('build_view_configuration');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('app_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('view_id', Types::STRING, [
				'notnull' => true,
				'length' => '128',
			]);
			$table->addColumn('config_key', Types::STRING, [
				'notnull' => true,
				'length' => '128',
			]);
			$table->addColumn('config_value', Types::TEXT, [
				'notnull' => false,
			]);

			$table->setPrimaryKey(['id'], 'id_viewcfg');
		}

		if (!$schema->hasTable('build_column_changelog')) {
			$table = $schema->createTable('build_column_changelog');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('value_id', Types::STRING, [
				'notnull' => true,
				'length' => '36',
				'fixed' => true,
			]);
			$table->addColumn('editor_type', Types::STRING, [
				// for now always 'user'
				'notnull' => true,
				'length' => '128',
			]);
			$table->addColumn('editor_id', Types::STRING, [
				// for now always a user id
				'notnull' => true,
				'length' => '128',
			]);
			$table->addColumn('old_val', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('new_val', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('mtime', Types::INTEGER, [
				'notnull' => false,
				'comment' => 'unix-timestamp',
			]);

			$table->setPrimaryKey(['id'], 'id_colchg');
		}

		return $schema;
	}
}
