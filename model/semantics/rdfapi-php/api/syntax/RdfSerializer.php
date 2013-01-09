<?php

// ----------------------------------------------------------------------------------
// Class: RdfSerializer
// ----------------------------------------------------------------------------------

/**
 * An RDF seralizer.
 * Seralizes models to RDF syntax. It supports the xml:base, xml:lang, rdf:datatype and
 * rdf:nodeID directive.
 * You can choose between different output syntaxes by using the configuration methods
 * or changing the configuration default values in constants.php.
 * This class is based on the java class edu.unika.aifb.rdf.api.syntax.RDFSerializer by Boris Motik.
 *
 * @version  $Id: RdfSerializer.php 316 2006-08-24 07:55:29Z tgauss $
 * @author Chris Bizer <chris@bizer.de>, Boris Motik <motik@fzi.de>, Daniel Westphal <dawe@gmx.de>, Leandro Mariano Lopez <llopez@xinergiaargentina.com>
 * @author Miel Vander Sande <miel.vandersande at ugent dot be>
 * 
 * @package syntax
 * @access  public
 *
 */
class RdfSerializer extends Object {

    // configuration
    private $use_entities;
    private $use_short_datatypes;
    private $use_attributes;
    private $sort_model;
    private $rdf_qnames;
    private $use_xml_declaration;
    // properties
    private $m_defaultNamespaces = array();
    private $m_namespaces = array();
    private $m_nextAutomaticPrefixIndex;
    private $m_out;
    private $m_baseURI;
    private $m_statements = array();
    private $m_currentSubject;
    private $m_rdfIDElementText;
    private $m_rdfAboutElementText;
    private $m_rdfResourceElementText;
    private $m_groupTypeStatement;
    private $m_attributeStatements = array();
    private $m_contentStatements = array();
    private $rdf_qname_prefix;

    /**
     * Constructor
     *
     * @access   public
     */
    public function RdfSerializer() {

        // default serializer configuration
        $this->use_entities = SER_USE_ENTITIES;
        $this->use_short_datatypes = SER_USE_SHORT_DATATYPES;
        $this->use_attributes = SER_USE_ATTRIBUTES;
        $this->sort_model = SER_SORT_MODEL;
        $this->rdf_qnames = SER_RDF_QNAMES;
        $this->use_xml_declaration = SER_XML_DECLARATION;


        global $default_prefixes;
        foreach ($default_prefixes as $key => $value) {
            $this->addNamespacePrefix($key, $value);
        }

        require_once(RDFAPI_INCLUDE_DIR . PACKAGE_UTILITY);
    }

    /**
     * Serializer congiguration: Sort Model
     * Flag if the serializer should sort the model by subject before serializing.
     * TRUE makes the RDF code more compact.
     * TRUE is default. Default can be changed in constants.php.
     *
     * @param     boolean
     * @access    public
     */
    public function configSortModel($bool) {
        $this->sort_model = $bool;
    }

    /**
     * Serializer congiguration: Use Entities
     * Flag if the serializer should use entities for URIs.
     * TRUE makes the RDF code more compact.
     * FALSE is default. Default can be changed in constants.php.
     *
     * @param     boolean
     * @access    public
     */
    public function configUseEntities($bool) {
        $this->use_entities = $bool;
    }

    /**
     * Serializer congiguration: Use Attributes
     * Flag if the serializer should serialize triples as XML attributes where possible.
     * TRUE makes the RDF code more compact.
     * FALSE is default. Default can be changed in constants.php.
     *
     * @param     boolean
     * @access    public
     */
    public function configUseAttributes($bool) {
        $this->use_attributes = $bool;
    }

    /**
     * Serializer congiguration: Use Qnames
     * Flag if the serializer should use qualified names for RDF reserved words.
     * TRUE makes the RDF code more compact.
     * TRUE is default. Default can be changed in constants.php.
     *
     * @param     boolean
     * @access    public
     */
    public function configUseQnames($bool) {
        $this->rdf_qnames = $bool;
    }

    /**
     * Serializer congiguration: Use XML Declaration
     * Flag if the serializer should start documents with the xml declaration
     * <?xml version="1.0" encoding="UTF-8" ?>.
     * TRUE is default. Default can be changed in constants.php.
     *
     * @param     boolean
     * @access    public
     */
    public function configUseXmlDeclaration($bool) {
        $this->use_xml_declaration = $bool;
    }

    /**
     * Adds a new prefix/namespace combination.
     *
     * @param     String $prefix
     * @param     String $namespace
     * @access    public
     */
    public function addNamespacePrefix($prefix, $namespace) {
        $this->m_defaultNamespaces[$prefix] = $namespace;
    }

    /**
     * Serializes a model to RDF syntax.
     * RDF syntax can be changed by config_use_attributes($boolean), config_use_entities($boolean),
     * config_sort_model($boolean).
     * NOTE: There is only one default namespace allowed within an XML document.
     *       Therefore if SER_RDF_QNAMES in constants.php is set to FALSE and you pass
     *       another $xml_default_namespace as parameter, the model will be serialized
     *       as if SER_RDF_QNAMES were set to TRUE.
     *
     * @param     object MemModel $model
     * @param     String $encoding
     * @return    string
     * @access    public
     */
    public function serialize($model, $xml_default_namespace = NULL, $encoding = DEFAULT_ENCODING) {

        if ($xml_default_namespace) {

            if ($xml_default_namespace == RDF_NAMESPACE_URI) {
                $this->rdf_qnames = FALSE;
                unset($this->m_defaultNamespaces[RDF_NAMESPACE_PREFIX]);
            } elseif ($xml_default_namespace == RDF_SCHEMA_URI) {
                unset($this->m_defaultNamespaces[RDF_SCHEMA_PREFIX]);
            } elseif (!SER_RDF_QNAMES)
                $this->rdf_qnames = TRUE;

            $this->addNamespacePrefix(NULL, $xml_default_namespace);
        }

        //copy parsed namespaces
        $nsps = array();
        $nsps = $model->getParsedNamespaces();
        foreach ($this->m_defaultNamespaces as $prefix => $namespace) {
            if (!isset($nsps[$namespace]))
                $nsps[$namespace] = $prefix;
        }

        // define rdf prefix (qname or not)
        if ($this->rdf_qnames) {
            if (isset($nsps[RDF_NAMESPACE_URI])) {
                $this->rdf_qname_prefix = $nsps[RDF_NAMESPACE_URI] . ':';
            } else {
                $this->rdf_qname_prefix = RDF_NAMESPACE_PREFIX . ':';
            }
        } else {
            $this->rdf_qname_prefix = '';
        }
        // check if model is empty
        if ($model->size() == 0)
            return "<" . $this->rdf_qname_prefix . RDF_RDF . " xmlns:rdf='" . RDF_NAMESPACE_URI . "' />";

        foreach ($nsps as $ns => $pre) {
            $this->m_namespaces[$pre] = $ns;
        }


        // set base URI
        if ($model->getBaseURI() == NULL)
            $this->m_baseURI = "opaque:uri";
        else
            $this->m_baseURI = $model->getBaseURI();


        if ($this->sort_model) {
            // sort the array of statements

            foreach ($model->triples as $key => $statement) {
                $stmkey = $statement->getSubject()->getURI() .
                        $statement->getPredicate()->getURI() .
                        (is_a($statement->getObject(), 'Literal') ? '"' . $statement->getObject()->getLabel() . '"@' . $statement->getObject()->getLanguage() . '^^' . $statement->getObject()->getDatatype() : $statement->getObject()->getURI());
                $this->m_statements[$stmkey] = $statement;
            }
            ksort($this->m_statements);

            /*
              // Sort using the PHP usort() function. Slower :-(
              $this->m_statements = $model->triples;
              usort($this->m_statements, "statementsorter");
             */
        } else {
            $this->m_statements = $model->triples;
        }
        // collects namespaces
        $this->m_nextAutomaticPrefixIndex = 0;
        $this->collectNamespaces($model);

        // start writing the contents
        $this->m_out = "";
        if ($this->use_xml_declaration)
            $this->m_out .= '<?xml version="1.0" encoding="' . $encoding . '" ?>';
        if (!HIDE_ADVERTISE)
            $this->m_out.="<!-- Generated by RdfSerializer.php from RDF RAP. # http://www.wiwiss.fu-berlin.de/suhl/bizer/rdfapi/index.html !-->";

        // write entitie declarations
        if ($this->use_entities) {
            $this->m_out .= '<!DOCTYPE ' . $this->rdf_qname_prefix .
                    RDF_RDF . ' [';
            $this->writeEntityDeclarations();
            $this->m_out .= ']>';
        }

        // start the RDF text
        $this->m_out .= '<' . $this->rdf_qname_prefix . RDF_RDF;

        // write the xml:base
        if ($model->getBaseURI() != NULL)
            $this->m_out .= INDENTATION . 'xml:base="' . $model->getBaseURI() . '"';

        // write namespaces declarations
        $this->writeNamespaceDeclarations();
        $this->m_out .='>';

        // write triples
        $this->writeDescriptions();

        $this->m_out .='</' . $this->rdf_qname_prefix . RDF_RDF . '>';

        $this->m_namespaces = null;
        $this->m_statements = null;
        $this->m_currentSubject = null;
        $this->m_groupTypeStatement = null;
        $this->m_attributeStatements = null;
        $this->m_contentStatements = null;
        $this->m_rdfResourceElementText = null;

        return $this->m_out;
    }

    /**
     * Serializes a model and saves it into a file.
     * Returns FALSE if the model couldn't be saved to the file.
     *
     * @param     object MemModel $model
     * @param     String $encoding
     * @return    boolean
     * @access    public
     */
    public function saveAs($model, $filename, $encoding = DEFAULT_ENCODING) {
        // serialize model
        $RDF = $this->serialize($model, NULL, $encoding);

        //write serialized model to file
        $file_handle = @fopen($filename, 'w');
        if ($file_handle) {
            fwrite($file_handle, $RDF);
            fclose($file_handle);
            return TRUE;
        } else {
            return FALSE;
        };
    }

    /**
     * @access   private
     */
    private function writeEntityDeclarations() {
        foreach ($this->m_namespaces as $prefix => $namespace) {
            $this->m_out .= INDENTATION . '<!ENTITY ' . $prefix . " '" . $namespace . "'>";
        }
    }

    /**
     * @access   private
     */
    private function writeNamespaceDeclarations() {
        foreach ($this->m_namespaces as $prefix => $namespace) {

            if ($prefix == RDF_NAMESPACE_PREFIX && !$this->rdf_qnames) {

                if ($this->use_entities) {
                    $this->m_out .= INDENTATION . XML_NAMESPACE_DECLARATION_PREFIX .
                            '="&' . $prefix . ';"';
                } else {
                    $this->m_out .= INDENTATION . XML_NAMESPACE_DECLARATION_PREFIX .
                            '="' . $namespace . '"';
                }
            } else {
                if ($prefix == NULL)
                    $colon_prefix = $prefix;
                else
                    $colon_prefix = ":" . $prefix;

                if ($this->use_entities) {
                    $this->m_out .= INDENTATION . XML_NAMESPACE_DECLARATION_PREFIX
                            . $colon_prefix . '="&' . $prefix . ';"';
                } else {
                    $this->m_out .= INDENTATION . XML_NAMESPACE_DECLARATION_PREFIX
                            . $colon_prefix . '="' . $namespace . '"';
                }
            }
        }
    }

    /**
     * @access   private
     */
    private function writeDescriptions() {

        $this->m_groupTypeStatement = NULL;
        $this->m_attributeStatements = array();
        $this->m_contentStatements = array();
        $this->m_currentSubject = NULL;

        foreach ($this->m_statements as $key => $statement) {
            $subject = $statement->getSubject();
            $predicate = $statement->getPredicate();
            $object = $statement->getobject();

            // write Group and update current subject if nessesary
            if ($this->m_currentSubject == NULL || !$this->m_currentSubject->equals($subject)) {
                $this->writeGroup();
                $this->m_currentSubject = $subject;
            }

            // classify the statement
            if (($predicate->getURI() == RDF_NAMESPACE_URI . RDF_TYPE) && is_a($object, 'Resource') && !$this->m_groupTypeStatement) {
                $this->writeGroup();
                $this->m_groupTypeStatement = $statement;
            } elseif ($this->canAbbreviateValue($object) &&
                    $this->use_attributes &&
                    $this->checkForDoubleAttributes($predicate)) {
                if (is_a($object, 'Literal')) {
                    if ($object->getDatatype() == NULL) {
                        $this->m_attributeStatements[] = $statement;
                    } else {
                        $this->m_contentStatements[] = $statement;
                    }
                } else {
                    $this->m_attributeStatements[] = $statement;
                }
            } else {
                $this->m_contentStatements[] = $statement;
            }
        }
        $this->writeGroup();
    }

    /**
     * @access   private
     */
    private function writeGroup() {
        if ($this->m_currentSubject == NULL || ($this->m_groupTypeStatement == NULL && (count($this->m_attributeStatements) == 0) && (count($this->m_contentStatements) == 0)))
            return;
        if ($this->m_groupTypeStatement != NULL)
            $outerElementName = $this->getElementText($this->m_groupTypeStatement->getObject()->getURI());
        else
            $outerElementName = $this->rdf_qname_prefix . RDF_DESCRIPTION;
        $this->m_out .= '<';
        $this->m_out .= $outerElementName;

        $this->m_out .= ' ';


        $this->writeSubjectURI($this->m_currentSubject);

        // attribute Statements
        if ($this->use_attributes)
            $this->writeAttributeStatements();

        if (count($this->m_contentStatements) == 0)
            $this->m_out .= '/>';
        else {
            $this->m_out .= '>';

            // content statements
            $this->writeContentStatements();

            $this->m_out .= '</';
            $this->m_out .= $outerElementName;
            $this->m_out .= '>';
        }
        $this->m_groupTypeStatement = NULL;
        $this->m_attributeStatements = array();
        $this->m_contentStatements = array();
    }

    /**
     * @param object Node $predicate
     * @access   private
     */
    private function checkForDoubleAttributes($predicate) {
        foreach ($this->m_attributeStatements as $key => $statement) {
            if ($statement->getPredicate()->equals($predicate))
                return FALSE;
        }
        return TRUE;
    }

    /**
     * @param STRING $uri
     * @access   private
     */
    public function relativizeURI($uri) {
        $uri_namespace = RDFUtil::guessNamespace($uri);
        if ($uri_namespace == $this->m_baseURI) {
            return RDFUtil::guessName($uri);
        } else {
            return $uri;
        }
    }

    /**
     * @param object Node $subject_node
     *
     * @access   private
     */
    private function writeSubjectURI($subject_node) {
        $currentSubjectURI = $subject_node->getURI();
        $relativizedURI = $this->relativizeURI($currentSubjectURI);

        // if submitted subject ist a blank node, use rdf:nodeID
        if (is_a($this->m_currentSubject, 'BlankNode')) {
            $this->m_out .= $this->rdf_qname_prefix . RDF_NODEID;
            $this->m_out .= '="';
            $this->m_out .= $relativizedURI;
        } else {


            if (!($relativizedURI == $currentSubjectURI)) {
                $this->m_out .= $this->rdf_qname_prefix . RDF_ID;
                $this->m_out .= '="';
                $this->m_out .= $relativizedURI;
            } else {
                $this->m_out .= $this->rdf_qname_prefix . RDF_ABOUT;
                $this->m_out .= '="';
                $this->writeAbsoluteResourceReference($relativizedURI);
            };
        };
        $this->m_out .= '"';
    }

    /**
     * @access   private
     */
    private function writeAttributeStatements() {
        foreach ($this->m_attributeStatements as $key => $statement) {
            $this->m_out .= INDENTATION;
            $this->m_out .= $this->getElementText($statement->getPredicate()->getURI());
            $this->m_out .= '=';
            $value = $statement->getObject()->getLabel();
            $quote = $this->getValueQuoteType($value);
            $this->m_out .= $quote;
            $this->m_out .= $value;
            $this->m_out .= $quote;
        }
    }

    /**
     * @access   private
     */
    private function writeContentStatements() {
        foreach ($this->m_contentStatements as $key => $statement) {
            $this->m_out .= '<';
            $predicateElementText = $this->getElementText($statement->getPredicate()->getURI());
            $this->m_out .= $predicateElementText;

            if (is_a($statement->getObject(), 'Resource')) {
                $this->writeResourceReference($statement->getObject());
                $this->m_out .= '/>';
            } else {
                if (is_a($statement->getObject(), 'Literal')) {
                    if ($statement->getObject()->getDatatype() != NULL)
                        if ($statement->getObject()->getDatatype() == RDF_NAMESPACE_URI . RDF_XMLLITERAL) {
                            $this->m_out .= ' ' . RDF_NAMESPACE_PREFIX . ':' . RDF_PARSE_TYPE . '="' . RDF_PARSE_TYPE_LITERAL . '"';
                        } else {
                            //Miel: short datatype
                            $this->m_out .= ' ' . RDF_NAMESPACE_PREFIX . ':' . RDF_DATATYPE . '="';
                            $this->writeAbsoluteDatatypeReference($statement->getObject()->getDatatype());
                            $this->m_out .= '"';
                        }
                    if ($statement->getObject()->getLanguage() != NULL)
                        $this->m_out .= ' ' . XML_NAMESPACE_PREFIX . ':' . XML_LANG . '="' . $statement->getObject()->getLanguage() . '"';
                };
                $this->m_out .= '>';
                if ($statement->getObject()->getDatatype() == RDF_NAMESPACE_URI . RDF_XMLLITERAL) {
                    $this->m_out .= $statement->getObject()->getLabel();
                } else {
                    $this->writeTextValue($statement->getObject()->getLabel());
                }
                $this->m_out .= '</';
                $this->m_out .= $predicateElementText;
                $this->m_out .= '>';
            }
        }
    }

    /**
     * @param Object $object_node
     * @access   private
     */
    private function writeResourceReference($object_node) {
        $rebaseURI = $object_node->getURI();
        $this->m_out .= ' ';
        if (is_a($object_node, 'BlankNode')) {
            $this->m_out .= $this->rdf_qname_prefix . RDF_NODEID;
        } else {
            $this->m_out .= $this->rdf_qname_prefix . RDF_RESOURCE;
        };

        $this->m_out .= '="';
        $relativizedURI = $this->relativizeURI($rebaseURI);
        if (!($relativizedURI == $rebaseURI))
            if (!is_a($object_node, 'BlankNode'))
                $this->m_out .= '#' . $relativizedURI;
            else
                $this->m_out .= $relativizedURI;
        else
            $this->writeAbsoluteResourceReference($rebaseURI);
        $this->m_out .= '"';
    }

    /**
     * @param String $rebaseURI
     * @access   private
     */
    private function writeAbsoluteResourceReference($rebaseURI) {
        $namespace = RDFUtil::guessNamespace($rebaseURI);
        $localName = RDFUtil::guessName($rebaseURI);
        $text = $rebaseURI;
        if ($namespace != '' and ($this->use_entities)) {
            $prefix = array_search($namespace, $this->m_namespaces);
            $text = '&' . $prefix . ';' . $localName;
        } else
            $text = RDFUtil::escapeValue($text);
        $this->m_out .= $text;
    }

    /**
     * @param String $rebaseURI
     * @access   private
     */
    private function writeAbsoluteDatatypeReference($rebaseURI) {
        $namespace = RDFUtil::guessNamespace($rebaseURI);
        $localName = RDFUtil::guessName($rebaseURI);
        $text = $rebaseURI;
        if ($namespace != '' and ($this->use_short_datatypes)) {
            $prefix = array_search($namespace, $this->m_namespaces);
            $text = '&' . $prefix . ';' . $localName;
        } else
            $text = RDFUtil::escapeValue($text);
        $this->m_out .= $text;
    }

    /**
     * @param STRING $textValue
     * @access   private
     */
    private function writeTextValue($textValue) {
        if ($this->getValueQuoteType($textValue) == USE_CDATA)
            $this->writeEscapedCDATA($textValue);
        else
            $this->m_out .= $textValue;
    }

    /**
     * @param STRING $textValue
     * @access   private
     */
    private function writeEscapedCDATA($textValue) {
        $this->m_out .= '<![CDATA[' . $textValue . ']]>';
    }

    /**
     * @param STRING $textValue
     * @access   private
     */
    private function getValueQuoteType($textValue) {
        $quote = USE_ANY_QUOTE;
        $hasBreaks = FALSE;
        $whiteSpaceOnly = TRUE;
        for ($i = 0; $i < strlen($textValue); $i++) {
            $c = $textValue{$i};
            if ($c == '<' || $c == '>' || $c == '&')
                return USE_CDATA;
            if ($c == LINEFEED)
                $hasBreaks = TRUE;
            if ($c == '"' || $c == "\'") {
                if ($quote == USE_ANY_QUOTE)
                    $quote = ($c == '"') ? "\'" : "\"";
                elseif ($c == $quote)
                    return USE_CDATA;
            }
            if (!($c == ' '))
                $whiteSpaceOnly = FALSE;
        }
        if ($whiteSpaceOnly || $hasBreaks)
            return USE_CDATA;
        return $quote == USE_ANY_QUOTE ? '"' : $quote;
    }

    /**
     * @param object Node $node
     * @access   private
     */
    private function canAbbreviateValue($node) {
        if (is_a($node, 'Literal')) {
            $value = $node->getLabel();
            if (strlen($value) < MAX_ALLOWED_ABBREVIATED_LENGTH) {
                $c = $this->getValueQuoteType($value);
                return $c == '"' || $c == '\'';
            }
        }
        return FALSE;
    }

    /**
     * @param STRING $elementName
     * @access   private
     */
    private function getElementText($elementName) {
        $namespace = RDFUtil::guessNamespace($elementName);
        $localName = RDFUtil::guessName($elementName);
        if ($namespace == "")
            return $localName;
        $prefix = array_search($namespace, $this->m_namespaces);

        if ($prefix === FALSE) {
            $errmsg = RDFAPI_ERROR . "(class: Serializer; method: getElementText): Prefix for element '" . $elementName . "' cannot be found.";
            trigger_error($errmsg, E_USER_ERROR);
        }

        //If the key is numeric, the output in for instance XML will be invalid
        //because tagnames cannot start with a number. We add an underscore
        if (is_numeric(substr($localName, 0, 1)))
            $localName = "_" . $localName;


        switch ($prefix) {
            case RDF_NAMESPACE_PREFIX:
                return $this->rdf_qname_prefix . $localName;
            case NULL:
                return $localName;
            default:
                return $prefix . ":" . $localName;
        }
    }

    /**
     * @param object MemModel $model
     * @access   private
     */
    private function collectNamespaces($model) {
        foreach ($model->triples as $key => $value) {
            if ($this->use_entities) {
                $this->collectNamespace($value->getSubject());
                if (!is_a($value->getObject(), 'Literal'))
                    $this->collectNamespace($value->getObject());
            } else {
                if ($value->getPredicate()->getURI() == RDF_NAMESPACE_URI . RDF_TYPE)
                    $this->collectNamespace($value->getObject());
                elseif
                (($value->getPredicate()->getURI() == RDF_NAMESPACE_URI . RDFS_SUBCLASSOF) ||
                        ($value->getPredicate()->getURI() == RDF_NAMESPACE_URI . RDFS_SUBPROPERTYOF)) {
                    $this->collectNamespace($value->getSubject());
                    $this->collectNamespace($value->getObject());
                }
            }

            $this->collectNamespace($value->getPredicate());
        }
    }

    /**
     * @param object Resource $resource
     * @access   private
     */
    private function collectNamespace($resource) {
        $namespace = RDFUtil::getNamespace($resource);
        if (!in_array($namespace, $this->m_namespaces) && $namespace != '') {
            $prefix = array_search($namespace, $this->m_defaultNamespaces);
            if ($prefix === FALSE)
                $prefix = $this->getNextNamespacePrefix();
            $this->m_namespaces[$prefix] = $namespace;
        }
    }

    /**
     * @access   private
     */
    private function getNextNamespacePrefix() {
        $this->m_nextAutomaticPrefixIndex++;
        return GENERAL_PREFIX_BASE . $this->m_nextAutomaticPrefixIndex;
    }

}

?>