<?php
// +----------------------------------------------------------------------
// | TP-Admin [ �๦�ܺ�̨����ϵͳ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2016 http://www.hhailuo.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: ��ң����־�� <xiaoyao.working@gmail.com>
// +----------------------------------------------------------------------

namespace Logic;

use Lib\Log;
/**
 * Logic ����
 * @author ��־�� <lizhiliang@kankan.com>
 */
class BaseLogic {
    protected $errorCode = 0;
    protected $errorMessages = array('0' => '');
    protected $errorMessage = '';
    /**
     * �ӿڷ��صĴ�����Ϣ
     * @var null
     */
    protected $serviceErrorInfo = null;
    protected static $_instances = array();

    public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class();
        }
        return self::$_instances[$class];
    }

    public function getInterfaceData($concurrence_curl_manager) {
        $response = $concurrence_curl_manager->getResponse();
        $temp = false;
        if ($response['code'] == 200) {
            $response_data = json_decode($response['data'], true);
            if ($response_data['code'] === 0) {
                $temp = isset($response_data['data']) ? $response_data['data'] : '';
                if ($response['time'] > 0.2) {
                    // ��¼����ѯ�ӿ�
                    Log::notice("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                }
            } else {
                // ��¼�ӿڷ��ش�������
                $this->errorCode = $response_data['code'];
                $this->serviceErrorInfo = $response_data;
                Log::warn("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                return false;
            }
        } else {
            // ��¼�ӿ��������
            Log::error("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            return false;
        }
        return $temp;
    }
    //������Ϣ�޸�
    public function getActorInfoInterfaceData($concurrence_curl_manager) {
        $response = $concurrence_curl_manager->getResponse();
        $response_data = false;
        if ($response['code'] == 200) {
            $response_data = json_decode($response['data'], true);
            if ($response_data['code'] == 0) {
                if ($response['time'] > 0.2) {
                    // ��¼����ѯ�ӿ�
                    Log::notice("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                }
            } else {
                // ��¼�ӿڷ��ش�������
                $this->errorCode = $response_data['code'];
                $this->serviceErrorInfo = $response_data;
                Log::warn("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                //return false;
            }
        } else {
            // ��¼�ӿ��������
            Log::error("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            return false;
        }
        return $response_data;
    }

    //��Ϣ����
    public function getMessageInterfaceData($concurrence_curl_manager) {
        $response = $concurrence_curl_manager->getResponse();
        $response_data = false;
        if ($response['code'] == 200) {
            $response_data = json_decode($response['data'], true);
            if ($response_data['status'] === 200) {
                if ($response['time'] > 0.2) {
                    // ��¼����ѯ�ӿ�
                    Log::notice("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                }
            } else {
                // ��¼�ӿڷ��ش�������
                $this->errorCode = $response_data['status'];
                $this->serviceErrorInfo = $response_data;
                Log::warn("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                return false;
            }
        } else {
            // ��¼�ӿ��������
            Log::error("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            return false;
        }
        return $response_data;
    }

    public function getLiveInterfaceData($concurrence_curl_manager) {
        $response = $concurrence_curl_manager->getResponse(); //var_dump($response);exit;
        if ($response['code'] == 200) {
            $response_data = json_decode($response['data'], true);
            if ($response_data['code'] === 0) {
                if ($response['time'] > 0.2) {
                    // ��¼����ѯ�ӿ�
                    Log::notice("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                }
            } else {
                // ��¼�ӿڷ��ش�������
                $this->errorCode = $response_data['code'];
                $this->serviceErrorInfo = $response_data;
                Log::warn("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            }
        } else {
            // ��¼�ӿ��������
            Log::error("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            return false;
        }
        return $response_data;
    }

    public function getIssueInterfaceData($concurrence_curl_manager) {//��ȡ��������
        $response = $concurrence_curl_manager->getResponse();
        $temp = false;
        if ($response['code'] == 200) {
            $response_data = json_decode($response['data'], true);
            if ($response_data['rtn'] === 0) {
                $temp = isset($response_data['data']) ? $response_data : '';
                if ($response['time'] > 0.2) {
                    // ��¼����ѯ�ӿ�
                    Log::notice("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                }
            } else {
                // ��¼�ӿڷ��ش�������
                $this->errorCode = $response_data['rtn'];
                $this->serviceErrorInfo = $response_data;
                Log::warn("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
                return false;
            }
        } else {
            // ��¼�ӿ��������
            Log::error("CURL REQUEST ERROR : HTTP_CODE=" . $response['code'] . '; TOTAL_TIME=' . $response['time'] . "; EFFECTIVE_URL=" . $response['url'] . '; Data :' . $response['data']);
            return false;
        }
        return $temp;
    }

    public function getErrorMessage() {
        return empty($this->errorMessage) ? (isset($errorMessages[$this->errorCode]) ? $errorMessages[$this->errorCode] : '') : $this->errorMessage;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

    public function getServiceErrorInfo() {
        return empty($this->serviceErrorInfo) ? false : $this->serviceErrorInfo;
    }

    public function verifyCodeValidate($verify_code) {
        $session_verify_code = session('verify_code');
        session('verify_code', null);
        return $verify_code == $session_verify_code;
    }
}