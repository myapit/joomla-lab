<?php
/**
* original code from : https://github.com/robwent/joomla-json-db-check
* Credit to him
* Turn on outputbuffering for servers that have it disabled.
*/
ob_start();
 
	<?php
	//Initiate Joomla so we can use it's functions
	/**
	* Constant that is checked in included files to prevent direct access.
	* define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
	*/
	define('_JEXEC', 1);

	if (file_exists(__DIR__ . '/defines.php'))
	{
		include_once __DIR__ . '/defines.php';
	}

	if (!defined('_JDEFINES'))
	{
		define('JPATH_BASE', __DIR__);
		require_once JPATH_BASE . '/includes/defines.php';
	}

	require_once JPATH_BASE . '/includes/framework.php';

	// Instantiate the application.
	$app    = JFactory::getApplication('site');
	$db     = JFactory::getDbo();
	$config = JFactory::getConfig();

	$jinput    = $app->input;
	$fullcheck = $jinput->get('fullcheck', 0, 'INT');

	function is_trying_to_be_json($data)
	{
		$data = trim($data);

		return ((substr($data, 0, 1) === '{') || (substr($data, -1, 1) === '}')) ? true : false;
	}

	function is_json()
	{
		call_user_func_array('json_decode', func_get_args());

		return (json_last_error() === JSON_ERROR_NONE);
	}

	//We use this for both checks
	/*
	UPDATE healtyv2_users SET params = REPLACE(params,'&quot;','"');
UPDATE healtyv2_categories SET params = REPLACE(params,'&quot;','"');
UPDATE healtyv2_categories SET rules = REPLACE(rules,'&quot;','"');
UPDATE healtyv2_categories SET metadata= REPLACE(metadata,'&quot;','"');
UPDATE healtyv2_content SET images = REPLACE(images,'&quot;','"');
UPDATE healtyv2_content SET urls = REPLACE(urls,'&quot;','"');
UPDATE healtyv2_content SET attribs = REPLACE(attribs,'&quot;','"');
*/
	$query = $db->getQuery(true)
	->select('TABLE_NAME,COLUMN_NAME')
	->from('INFORMATION_SCHEMA.COLUMNS')
	->where('COLUMN_NAME = \'params\' OR COLUMN_NAME = \'rules\' OR COLUMN_NAME = \'metadata\'') 
	->andWhere('TABLE_SCHEMA = \'' . $config->get('db') . '\'');

	$db->setQuery($query);
	$results = $db->loadObjectList();
	?>
	<?php if ($fullcheck == 0) : ?>
		<h4>Checking for Invalid Empty Parameters</h4>
		<?php
		if ($results)
		{
			foreach ($results as $result)
			{
				echo "Checking table: {$result->TABLE_NAME}, column {$result->COLUMN_NAME}<br>";
				$query = $db->getQuery(true)
				->update($result->TABLE_NAME)
				->set($result->COLUMN_NAME . ' = "{}"')
					->where($result->COLUMN_NAME . ' = "" OR ' . $result->COLUMN_NAME . ' = \'{\"\"}\' OR ' . $result->COLUMN_NAME . ' = \'{\\\\\"\\\\\"}\' ');

					$db->setQuery($query);
					$results = $db->execute();
					$changes = $db->getAffectedRows();

					if ($changes != 0)
					{
						echo $changes . " rows modified.<br>";
					}
				}
			}
			?>
			<h4>Finished checking empty parameters</h4>
			<form>
				<button class="btn" name="fullcheck" value="1">Check For All Invalid Values</button>
			</form>
			<p></p>
			<p><small>(This will not replace any values, you will need to manaully fix them)</small></p>
		<?php else : ?>
			<h4>Checking all Params and Rules Entries for Invalid Syntax</h4>
			<?php
			// Check all params for invalid syntax
			if ($results)
			{
				foreach ($results as $result)
				{
					echo "<p>Checking table: {$result->TABLE_NAME}, column {$result->COLUMN_NAME}</p>";
					$query = $db->getQuery(true)
					->select('*')
					->from($result->TABLE_NAME)
					->where($result->COLUMN_NAME . ' != "{}"');

						$db->setQuery($query);

						$results = $db->loadAssocList();

						if ($results)
						{
							foreach ($results as $row)
							{
								if (!is_json($row[$result->COLUMN_NAME]) && is_trying_to_be_json($row[$result->COLUMN_NAME]))
								{
									$error = json_last_error_msg();
									reset($row);
									$first_key = key($row);
									echo "Row {$row[$first_key]} is not valid JSON. Error: ($error)<br>";
									echo "Content: {$row[$result->COLUMN_NAME]}<br><hr>";
									/*
									* Fix Code Begin Here ...
									* REPLACE(params,'&quot;','"')
									*/
									$tableFix = $result->TABLE_NAME;
									$columnFix = $result->COLUMN_NAME;
									$sqlFix = 'UPDATE '. $tableFix.' SET '.$columnFix.' REPLACE('.$columnFix.',\'&quot;\',\'"\')';
									echo "<br\>".$sqlFix."<br\>";
									$resultFix = $db->setQuery($sqlFix);
									$db->execute();

									UPDATE #__content SET images = REPLACE(images,'&quot;','"');
									UPDATE #__content SET urls = REPLACE(urls,'&quot;','"');
									UPDATE #__content SET attribs = REPLACE(attribs,'&quot;','"');
									if ($resultFix)
									{ 
										echo "Table $tableFix Replaced.";
									}else { 
										echo "Table $tableFix FAILED !!";
									}

									/*
									*Fix Code End Here*
									*/

								}
							}
						}
					}
				}?>

				<h4>Finished checking invalid parameters</h4>
				<p>Check invalid rules at <a target="_blank" href="http://jsonlint.com/">jsonlint.com</a></p>
				<form>
					<button class="btn" name="fullcheck" value="1">Check Again</button>
				</form>

			<?php endif; ?>
