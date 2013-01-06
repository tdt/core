<?php

/**
 * EasyRdf autoloader for TDTFramework
 *
 * LICENSE
 *
 * Copyright (c) 2009-2011 Nicholas J Humfrey.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. The name of the author 'Nicholas J Humfrey" may be used to endorse or
 *    promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    EasyRdf
 * @copyright  Copyright (c) 2011 Nicholas J Humfrey
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @version    $Id$
 */

AutoInclude::bulkRegister(array(
                              "EasyRdf_Exception" => "includes/EasyRdf/Exception.php",
                              "EasyRdf_Format" => "includes/EasyRdf/Format.php",
                              "EasyRdf_Graph" => "includes/EasyRdf/Graph.php",
                              "EasyRdf_GraphStore" => "includes/EasyRdf/GraphStore.php",
                              "EasyRdf_Http" => "includes/EasyRdf/Http.php",
                              "EasyRdf_Http_Client" => "includes/EasyRdf/Http/Client.php",
                              "EasyRdf_Http_Response" => "includes/EasyRdf/Http/Response.php",
                              "EasyRdf_Namespace" => "includes/EasyRdf/Namespace.php",
                              "EasyRdf_Literal" => "includes/EasyRdf/Literal.php",
                              "EasyRdf_Literal_Boolean" => "includes/EasyRdf/Literal/Boolean.php",
                              "EasyRdf_Literal_Date" => "includes/EasyRdf/Literal/Date.php",
                              "EasyRdf_Literal_DateTime" => "includes/EasyRdf/Literal/DateTime.php",
                              "EasyRdf_Literal_Decimal" => "includes/EasyRdf/Literal/Decimal.php",
                              "EasyRdf_Literal_HexBinary" => "includes/EasyRdf/Literal/HexBinary.php",
                              "EasyRdf_Literal_Integer" => "includes/EasyRdf/Literal/Integer.php",
                              "EasyRdf_ParsedUri" => "includes/EasyRdf/ParsedUri.php",
                              "EasyRdf_Parser" => "includes/EasyRdf/Parser.php",
                              "EasyRdf_Parser_RdfPhp" => "includes/EasyRdf/Parser/RdfPhp.php",
                              "EasyRdf_Parser_Ntriples" => "includes/EasyRdf/Parser/Ntriples.php",
                              "EasyRdf_Parser_Json" => "includes/EasyRdf/Parser/Json.php",
                              "EasyRdf_Parser_RdfXml" => "includes/EasyRdf/Parser/RdfXml.php",
                              "EasyRdf_Parser_Turtle" => "includes/EasyRdf/Parser/Turtle.php",
                              "EasyRdf_Resource" => "includes/EasyRdf/Resource.php",
                              "EasyRdf_Serialiser" => "includes/EasyRdf/Serialiser.php",
                              "EasyRdf_Serialiser_GraphViz" => "includes/EasyRdf/Serialiser/GraphViz.php",
                              "EasyRdf_Serialiser_RdfPhp" => "includes/EasyRdf/Serialiser/RdfPhp.php",
                              "EasyRdf_Serialiser_Ntriples" => "includes/EasyRdf/Serialiser/Ntriples.php",
                              "EasyRdf_Serialiser_Json" => "includes/EasyRdf/Serialiser/Json.php",
                              "EasyRdf_Serialiser_RdfXml" => "includes/EasyRdf/Serialiser/RdfXml.php",
                              "EasyRdf_Serialiser_Turtle" => "includes/EasyRdf/Serialiser/Turtle.php",
                              "EasyRdf_Sparql_Client" => "includes/EasyRdf/Sparql/Client.php",
                              "EasyRdf_Sparql_Result" => "includes/EasyRdf/Sparql/Result.php",
                              "EasyRdf_TypeMapper" => "includes/EasyRdf/TypeMapper.php",
                              "EasyRdf_Utils" => "includes/EasyRdf/Utils.php"
                          ));
