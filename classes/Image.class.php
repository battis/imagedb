<?php

class Image extends Item
{
	/*************************************************************************
	 * Properties
	 *************************************************************************/

	/**
	 * The calculated hash value of the object's file
	 * @var str
	 **/
	protected $hash;


	/*************************************************************************
	 * Constructors
	 *************************************************************************/

	/**
	 * @param str $path The path of the Image relative to the Container root
	 * @param Container $container An initialized container object which "holds" this Image
	 * @see Item#pathConstructor()
	 **/
	protected function pathConstructor($path, Container $container)
	{
		parent::pathConstructor($path, $container);
		$this->calculateHash();
	}


	/*************************************************************************
	 * Methods
	 *************************************************************************/

	/**
	 * Calculate the MD5 hash of the image file and update the database (if changed)
	 * @return The current hash value
	 **/
	public function calculateHash()
	{
		$filePath = concatenatePath($this->container->getPath(), $this->path);
		$imageFile = fopen($filePath, "rb");
		$hash = md5(fread($imageFile, filesize($filePath)));
		fclose($imageFile);

		if ($hash != $this->hash)
		{
			$this->hash = $hash;
			$this->database->query("UPDATE `objects` SET `hash` = '{$this->hash}' WHERE `id` = '{$this->id}'");
		}

		return $this->hash;
	}


	/*************************************************************************
	 * Accessor methods
	 *************************************************************************/

	/**
	 * @return The hash value of the object's file
	 **/
	public function getHash()
	{
		return $this->hash;
	}


	/*************************************************************************
	 * Conversion methods
	 *************************************************************************/

	/**
	 * @param str $subclassProperties Any additional properties defined by subclasses
	 * @param bool $showDatabaseObject Show the expanded database object (defaults to false)
	 * @param bool $showExpandedContainerObject Show the expanded container of this object (defaults to false: show only ID number and name)
	 * @return An HTML-formatted string version of this Image object
	 **/
	public function toHTML($subclassProperties = "", $showDatabaseObject = false, $showExpandedContainerObject = false)
	{
		return parent::toHtml(
			"<li class=\"Image-toHtml\">`hash`: " . toHTML ($this->hash) . "</li>" .
			$subclassProperties,
			$showDatabaseObject,
			$showExpandedContainerObject
		);
	}
}