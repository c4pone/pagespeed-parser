<?php namespace c4pone\PageSpeed;

class Parser {

    private $data;

    public function __construct(array $api_response)
    {
        $this->data = $api_response;    
    }

    /**
     * Returns the current use api response
     *
     * @return array
     */
    public function getApiResponse()
    {
        return $this->data;
    }

    /**
     * Sets the api_response that the parser works on
     */
    public function setApiResponse(array $api_response)
    {
        $this->data = $api_response;
    }

    /**
     * Returns the title of the webpage
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title', '');
    }

    /**
     * Returns the page spats
     *
     * @return array
     */
    public function getPageStats()
    {
        return $this->get('pageStats', []);
    }

    /**
     * Returns a screenshot object
     *
     * @return Screenshot
     */
    public function getScreenshot()
    {
        $screenshot = new Screenshot();

        if ($this->get('screenshot', false) != false) {
            $screenshot->setData($this->get('screenshot.data', ''));
        }

        return $screenshot;
    }

    /**
     * Returns the score for each rule group
     *
     * @return array
     */
    public function getScores()
    {
        $results = array();

        $ruleGroups = $this->get('ruleGroups', []);
        foreach ($ruleGroups as $group => $score) {
            $results[$group] = $score['score'];
        }

        return $results;
    }
    
    /**
     * Returns the score of a specific key.
     *
     * @param String $key
     * @return String
     */
    public function getScore($key)
    {
        return $this->getScores()[$key] ?? false;
    }

    /**
     * Returns all the recommendations.
     * All the parameter are replaced with it's value already
     *
     * @return array
     */
    public function getRecommendations()
    {
        $recommendations = array();

        foreach($this->get('formattedResults.ruleResults',[]) as $name => $ruleResult) {
            $this->parseRecommendation($recommendations, $name, $ruleResult);
        }
        
        return $recommendations;
    }


    /**
     * Parses a rule result and adds it to the result
     *
     * @param string $recommendation_name
     * @param array $api_response
     */
    protected function parseRecommendation(&$recommendations, $recommendationName, array $api_response)
    {
        $recommendation = array(
            'name' => $api_response['localizedRuleName'],
        );

        //groups in which the recommendation belongs
        $groups = $api_response['groups'];

        //parse summary
        if (isset($api_response['summary'])) {
            $recommendation['summary'] = $this->parseFormat($api_response['summary']);
        }

        //parse urlBlocks
        if (isset($api_response['urlBlocks'])) {
            $recommendation['urlBlocks'] = $this->parseUrlBlocks($api_response['urlBlocks']);
        }

        foreach ($groups as $group) {
            $recommendations[$group][$recommendationName] = $recommendation;
        }
    }

    /**
     * Parses all the url blocks and returns the parsed result
     *
     * @param array $urlBlocks
     * @return array
     */
    protected function parseUrlBlocks(array $urlBlocks)
    {
        $blocks = array();

        foreach ($urlBlocks as $urlBlock) {
            $block = array();

            if (isset($urlBlock['header'])) {
                $block['header'] = $this->parseFormat($urlBlock['header']);
            }

            if (isset($urlBlock['urls'])) {
                $urls = array();
                foreach($urlBlock['urls'] as $url) {
                    $urls[] = $this->parseFormat($url['result']);
                }

                $block['urls'] = $urls;
            }

            $blocks[] = $block;
        }

        return $blocks;
    }

    /**
     * Replaces all the placeholder with it's values
     * and returns the parsed result
     *
     * @param array $api_response
     * @return string
     */
    protected function parseFormat(array $api_response)
    {
        $format = $api_response['format'];

        //if arguments are given replace them in the format
        if (isset ($api_response['args'])) {
            foreach ($api_response['args'] as $arg) {
                $key    = $arg['key'];
                $type   = $arg['type'];
                $value  = $arg['value'];

                //hyperlink has a beginning and ending
                if ($type == 'HYPERLINK') {
                    $format = str_replace(
                        '{{BEGIN_LINK}}', 
                        "<a href=\"{$value}\">",
                        $format);

                    $format = str_replace(
                        '{{END_LINK}}', 
                        "</a>",
                        $format);
                } else {
                    $format = str_replace(
                        '{{'.$key.'}}',
                        $value,
                        $format);
                }
            }
        }

        return $format; 
    
    }

    private function get($key, $default)
    {
        return array_get($this->data, $key, $default);
    }
}
