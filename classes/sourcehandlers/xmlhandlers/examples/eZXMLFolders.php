<?php 

class eZXMLFolders extends eZXMLHandlerPHP
{
	/* (non-PHPdoc)
	 * @see SourceHandler::readData()
	*/
	public function readData()
	{
		return $this->parse_xml_document( 'extension/data_import/dataSource/examples/folder_structure.ezxml', 'all' );
	}
}
