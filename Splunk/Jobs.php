<?php
/**
 * Copyright 2012 Splunk, Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"): you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
 * Represents the collection of all search jobs.
 * 
 * @package Splunk
 */
class Splunk_Jobs extends Splunk_Collection
{
    // === Operations ===
    
    /**
     * Creates a new search job.
     * 
     * @param string $search    The search query for the job to perform.
     * @param array $args   (optional) Job-specific creation arguments,
     *                      merged with {
     *     'namespace' => (optional) {Splunk_Namespace} The namespace in which
     *                    to create the entity. Defaults to the service's
     *                    namespace.
     * }
     *                      For details, see the
     *                      "POST search/jobs"
     *                      endpoint in the REST API Documentation.
     * @return Splunk_Job
     * @throws Splunk_HttpException
     * @link http://docs.splunk.com/Documentation/Splunk/4.3.3/RESTAPI/RESTsearch#search.2Fjobs
     */
    public function create($search, $args=array())
    {
        $args = array_merge(array(
            'search' => $search,
        ), $args);
        
        if (array_key_exists('exec_mode', $args) && ($args['exec_mode'] === 'oneshot'))
            throw new InvalidArgumentException(
                'Cannot create oneshot jobs with this method. Use createOneshot() instead.');
        
        $response = $this->service->post($this->path, $args);
        $xml = new SimpleXMLElement($response->body);
        $sid = Splunk_XmlUtil::getTextContentAtXpath($xml, '/response/sid');
        return $this->getReference($sid, Splunk_Namespace::createDefault());
    }
    
    /**
     * Executes the specified search query and returns results immediately.
     * 
     * @param string $search    The search query for the job to perform.
     * @param array $args   (optional) Job-specific creation arguments,
     *                      merged with {
     *     'namespace' => (optional) {Splunk_Namespace} The namespace in which
     *                    to create the entity. Defaults to the service's
     *                    namespace.
     * }
     *                      For details, see the
     *                      "POST search/jobs"
     *                      endpoint in the REST API Documentation.
     * @return string           The search results, which can be parsed with
     *                          Splunk_ResultsReader.
     * @throws Splunk_HttpException
     * @link http://docs.splunk.com/Documentation/Splunk/4.3.3/RESTAPI/RESTsearch#search.2Fjobs
     */
    public function createOneshot($search, $args=array())
    {
        $args = array_merge(array(
            'search' => $search,
            'exec_mode' => 'oneshot',
        ), $args);
        
        if ($args['exec_mode'] !== 'oneshot')
            throw new InvalidArgumentException(
                'Cannot override "exec_mode" with value other than "oneshot".');
        
        $response = $this->service->post($this->path, $args);
        return $response->body;
    }
}