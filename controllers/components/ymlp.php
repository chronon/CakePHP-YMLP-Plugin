<?php
class YmlpComponent extends Object {

    private $__apiUrl = 'https://www.ymlp.com/api/';

    public function initialize($controller, $settings = array()) {
        $this->controller = $controller;
        $this->settings = Configure::read('Ymlp.settings');
        $this->fieldMap = Configure::read('Ymlp.fieldMap');
	}

    public function utility($method, $data = array()) {
        $result = $this->__makePost($method, $data);
        $result = unserialize($result);
        return $result;
    }

    public function contactsAdd($data, $force = false) {
        $data = $this->__prepPost($data);
        if ($force) {
            $data['OverruleUnsubscribedBounced'] = 1;
        }
        $result = $this->__makePost('Contacts.Add', $data);
        $message = $this->__setOutput($result);
        return $message;
    }

    public function contactsUnsubscribe($data) {
        $data = $this->__prepPost($data, true);
        $result = $this->__makePost('Contacts.Unsubscribe', $data);
        $message = $this->__setOutput($result);
        return $message;
    }

    private function __prepPost($data, $emailOnly = false) {
        $fields = array();
        foreach ($this->fieldMap as $local => $id) {
            if ($id == 'Email') {
                $fields["$id"] = $data[$local];
                if ($emailOnly) {
                    return $fields;
                }
            } else {
                $fields["Field$id"] = $data[$local];
            }
        }
        return $fields;
    }

    private function __setOutput($result) {
        if ($result) {
            $result = unserialize($result);
            $message = 'Code '.$result['Code'];
            $message .= ': '.$result['Output'];
        } else {
            $message = 'YMLP connection error!';
        }
        return $message;
    }

	private function __makePost($method, $data) {

        // complete the url
        $url = $this->__apiUrl.$method;

        // merge the config data and the submitted data
        $data = array_merge($this->settings, $data);

        // turn the data array into a string
        $data = http_build_query($data);

	    $defaults = array(
	        CURLOPT_POST => 1,
	        CURLOPT_HEADER => 0,
	        CURLOPT_URL => $url,
	        CURLOPT_FRESH_CONNECT => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FORBID_REUSE => 1,
	        CURLOPT_TIMEOUT => 15,
	        CURLOPT_POSTFIELDS => $data,
	    );

	    $ch = curl_init();
	    curl_setopt_array($ch, $defaults);
	    if (!$result = curl_exec($ch)) {
			return false;
	    }

	    curl_close($ch);
	    return $result;
	}

}