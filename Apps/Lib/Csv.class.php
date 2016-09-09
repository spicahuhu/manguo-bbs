<?php
namespace Lib;

/**
 *  CSV操作
 */
class Csv {
    /**
     * 导出数据到CSV文件
     * @param  array  $rows     文件数据
     * @param  string $filename 导出的文件名?
     */
    public static function export($header, $rows, $filename = '') {
        if (empty($header)) {
            return false;
        }
        if (empty($filename)) {
            $filename = 'ExportCSVFile'.date('Ymd').'.csv';
        }

        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);
        $fp = fopen('php://output', 'w');
        fputcsv($fp, self::_parseExportRow($header));
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $row = self::_parseExportRow($row);
                fputcsv($fp, $row);
            }
        }
        fclose($fp);
    }

    /**
     * 读取文件内容
     * @param  string   $filename    文件名称
     */
    public static function readData($filename) {
        if (empty($filename)) {
            return false;
        }
        $fp = fopen($filename, 'r');
        if (!$fp) {
            return false;
        }
        $header = fgetcsv($fp);
        $data = array();
        while ($row = fgetcsv($fp)) {
            $data[] = self::_parseReadRow($row);
        }

        return array(
                'header' => self::_parseReadRow($header),
                'data'   => $data
            );
    }

    /**
     * 对导出行数据进行处理
     * @param  array   $row   导出的行数据
     */
    private static function _parseExportRow($row) {
        if (empty($row)) {
            return array();
        }
        array_walk($row, (function (&$val, $key){
            if (!is_scalar($val) || is_string($val)) {
                // 数据库中保存的数据可能被转义为html实体
                $val = html_entity_decode($val);
                // 导出的数据需要为gbk格式
                //$val = utf8_to_gbk($val); var_dump($val);
            }
        }));
        return $row;
    }

    /**
     * 对导入的数据进行处理
     */
    private static function _parseReadRow($row) {
        if (empty($row)) {
            return array();
        }
        // 导入的数据需要为utf8格式
        array_walk($row, (function (&$val, $key){
            if (!is_utf8($val)) {
                $val = gbk_to_utf8($val);
            }
        }));

        return $row;
    }
}