<?php

/**
 * @author pkamps
 *
 */
class SourceHandler
{
	public $data;
	public $current_row;
	public $current_field;

	public $idPrepend = 'remoteID_';
	/**
	 * @var string
	 */
	public $handlerTitle = 'Abstract Handler';

	/**
	 * @var
	 */
	protected $parameters;

	/**
	 * Name of file; is located in var/log
	 *
	 * @var string
	 */
	protected $logFile = 'data_import.log';

	public $db;
	public $node_priority = false;
		
	/**
	 * @var integer
	 */
	protected $fallbackParentNodeId = 2;
	
	/**
	 * @var ImportOperator
	 */
	public $import_operator;
	
	public function getPriorityForNode()
	{
		return $this->node_priority;
	}

	/**
	 * @param array $parameters
	 */
	public function init( array $parameters )
	{
		$this->parameters = $parameters;
		return $this;
	}
	
	/**
	 * Gets the next row from object var 'data'
	 * It may be necessary to implement a point for the data row
	 * Sets and returns the object var 'current_row'
	 * Returns false if no more rows are available
	 * 
	 * @return NULL
	 */
	public function getNextRow()
	{
		$this->current_row = null;
		return $this->current_row;
	}

	/**
	 * // sets the internal point to next field or return false
	 * 
	 * @return boolean
	 */
	public function getNextField()
	{
		return false;
	}

	/**
	 * Get the class attribute identifier for the current field
	 * 
	 * @return string
	 */
	public function geteZAttributeIdentifierFromField()
	{
		return '';
	}

	/**
	 * "fromString" value from data source for an eZ Attribute
	 * 
	 * @param eZContentObjectAttribute $contentObjectAttribute
	 * @return string
	 */
	public function getValueFromField( eZContentObjectAttribute $contentObjectAttribute )
	{
		return ''; 
	}

	/*
	 * Logic how to build the remote id
	 * 
	 * @return string
	 */
	public function getDataRowId()
	{
		return $this->idPrepend . 'actual_id_value';
	}

	/**
	 * the ezp target-class identifier
	 *
	 * @return string
	 */
	public function getTargetContentClass()
	{
		return 'folder';
	}

	/**
	 * Language idenfier
	 * 
	 * @return string
	 */
	public function getTargetLanguage()
	{
		return null;
	}

	/*
	 * Read the data source
	 * Can be an xml file, csv file or queries to a remote DB
	 * Sets object var "data"
	 */
	public function readData()
	{
		$this->data = null;
	}

	/**
	 * Method is called after all attributes are saved and
	 * before the node gets published
	 * 
	 * @param boolean $force_exit
	 * @return boolean
	 */
	public function post_save_handling( &$force_exit )
	{
		$force_quit = false;
		return true;
	}
	
	/**
	 * The method is called after the node was published
	 * 
	 * @param boolean $force_exit
	 * @return boolean
	 */
	public function post_publish_handling( &$force_exit )
	{
		$force_quit = false;
		return true;
	}

	
	public function updatePublished( $eZ_object )
	{
		return false;
	}
	
	/**
	 * Returns an array of eZContentObject attribute values like
	 * publish_date, owner etc
	 * 
	 * @return array
	 */
	public function getEzObjAttributes()
	{
		return array();
	}
	
	/**
	 * Returns an array of ez publish object state ids -- it's an array of integers.
	 * 
	 * @return array()
	 */
	public function getStateIds()
	{
		return array();
	}
	
	/**
	 * Override this method and return an array of node details as DOMElements
	 * TODO: review the return value
	 * 
	 * @return DOMNodeList
	 */
	public function getNodeAssignments()
	{
		$parent_node = $this->getParentNode();
		
		// Fallback node
		if( !( $parent_node instanceof eZContentObjectTreeNode ) )
		{
			$parent_node = eZContentObjectTreeNode::fetch( $this->fallbackParentNodeId );
		}

		// Create DomNode
		$xml = '<n is-main-node="1" remote-id="'. $this->getDataRowId() .'" parent-node-remote-id="'. $parent_node->attribute( 'remote_id' ) .'" />';

		$dom = new DOMDocument( '1.0', 'utf-8' );
		$dom->loadXML( $xml );

		return $dom->childNodes;
	}
	
	
	/**
	 * Override this method and implement you own logic how to get the parent node.
	 * In most cases, you want to read a node (or remote id) from the source file.
	 * 
	 * @return eZContentObjectTreeNode
	 */
	protected function getParentNode()
	{
		$parentNodeId = $this->fallbackParentNodeId;
		
		// Accept given parent node ID from command line
		if( isset( $this->parameters[ 'parentID' ] ) && (int) $this->parameters[ 'parentID' ] )
		{
			$parentNodeId = $this->parameters[ 'parentID' ];
		}
		
		return eZContentObjectTreeNode::fetch( $parentNodeId );
	}

	protected function log( $message, $lineBreak = true, $file = null )
	{
		$file = $file ? $file : $this->logFile;
		$message = $lineBreak ? $message . "\n" : $message;

		error_log( $message, 3, 'var/log/' . $file );
	}

	/*
	 * Implement this function if you want to get progress info in the output
	 */
	public function getRowCount()
	{
		return false;
	}
}
