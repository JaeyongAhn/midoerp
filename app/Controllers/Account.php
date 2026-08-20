<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class Account extends BaseController
{
    public function index(): string
    {
        if($this->session->get('logined'))
        {
            $res = $this->db->query("select * from account_id order by id desc limit 1")->getRowArray();
            if(!$res){
                $res = [
                    'last_account' => '0000-00-00 00:00:00'
                ];
            }
            return view('account', [
                'last_transdate' => $res['last_account']
            ]);
        }
        return '로그인이 필요합니다.';
    }

    public function load_tags()
    {
        $mode = $this->request->getGet('mode');

        $tags = $this->db->query("select name from tags where `mode` = ? order by id", [$mode])->getResultArray();
        return $this->response->setJSON([
                'data' => $tags
            ]);
    }

    public function save_tags()
    {
        $data = $this->request->getJSON(true);
        
        error_log(json_encode($data));
        $mode = $data['mode'];
        foreach($data['tags'] as $item){
            $row = $this->db->query("select count(1) cnt from tags where `mode` = ? and name = ?", [$mode, $item])->getRow();
            if($row->cnt > 0){
               return $this->response->setJSON([
                    'success' => false,
                    'item' => $item
                ]);
            }
        }

        foreach($data['tags'] as $item){
            $this->db->query("insert into tags(`mode`, name) values(?,?)", [$mode, $item]);
        }

        return $this->response->setJSON([
                    'success' => true
                ]);
    }

    public function save_tag()
    {
        $data = $this->request->getJSON(true);
        $mode = $data['mode'];
        $this->db->query("update account set tag{$mode} = ? where id = ?", [ $data['tag'], $data['id'] ]);
        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function do_upload()
    {
        $file = $this->request->getFile('excel');

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '파일 오류'
            ]);
        }

        // 임시 파일 경로
        $filePath = $file->getTempName();

        // Excel 읽기
        $spreadsheet = IOFactory::load($filePath);

        $sheet = $spreadsheet->getActiveSheet();

        // 데이터 읽기
        $rows = $sheet->toArray();

        $text = $rows[1][0];

        $text = preg_replace('/\s+/', '', $text);

        // 계좌번호
        preg_match('/계좌번호:([\d-]+)/', $text, $account);

        // 조회기준일
        preg_match('/조회기준일:(\d{4}년\s*\d{2}월\s*\d{2}일\s*\d{2}:\d{2})/', $text, $baseDate);

        // 현재잔액
        preg_match('/현재잔액:([\d,]+원)/', $text, $balance);

        // 조회시작일자
        preg_match('/조회시작일자:(\d{4}-\d{2}-\d{2})/', $text, $startDate);

        // 조회종료일자
        preg_match('/조회종료일자:(\d{4}-\d{2}-\d{2})/', $text, $endDate);

        $result = implode(':', [
            $account[1],
            $baseDate[1], // 공백 정리
            $balance[1],
            $startDate[1],
            $endDate[1]
        ]);

        $res = $this->db->query("select id from account_id where shorten = ?", [$result])->getRow();
        $data = [];
        if($res){
            $data = $this->db->query("select * from account where account_id = ? order by trans_date desc", [$res->id])->getResultArray();
        }else{
            $this->db->query("insert into account_id(shorten, last_account) values(?,?)", [$result, $rows[3][1]]);
            $id = $this->db->insertID();
            for($i=3; $i<count($rows)-1; $i++){
                $this->db->query("insert into account(account_id, trans_date, chulguem, ipguem, janeak, naeyong, memo) values(?,?,?,?,?,?,?)",
                 [$id, $rows[$i][1], str_replace(',','',$rows[$i][2]), str_replace(',','',$rows[$i][3]), str_replace(',','',$rows[$i][4]), $rows[$i][5], $rows[$i][8]]);
                 $data[] = ['id'=>$this->db->insertID(), 
                            'account_id' => $id, 
                            'trans_date' => $rows[$i][1], 
                            'chulguem' => str_replace(',','',$rows[$i][2]), 
                            'ipguem' => str_replace(',','',$rows[$i][3]), 
                            'janeak' => str_replace(',','',$rows[$i][4]), 
                            'naeyong' => $rows[$i][5], 
                            'memo' => $rows[$i][8], 
                            'tag' => null];
            }
        }

        if($rows[0][0] == '거래내역조회_입출식 예금'){
            return $this->response->setJSON([
                'success' => true,
                'banner' => $rows[1][0],
                'test' => json_encode($res),
                'data' => json_encode($data),
                'last' => $rows[3][1],
                'message' => 'Excel 읽기 완료'
            ]);
        }
        return $this->response->setJSON([
                'success' => false,
                'data' => '[0]',
                'message' => '기업은행 통장이 아닙니다.'
            ]);
    }

    public function search(){
        $data = $this->request->getJSON(true);

        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $searchTag1 = $data['tag1'];
        $searchTag2 = $data['tag2'];
        $searchTag3 = $data['tag3'];
        
        $builder = $this->db->table('account');

        if (!empty($startDate)) {
            $builder->where('trans_date >=', $startDate . ' 00:00:00');
        }

        if (!empty($endDate)) {
            $builder->where('trans_date <=', $endDate . ' 23:59:59');
        }

        if (!empty($searchTag1)) {
            foreach ($searchTag1 as $tag) {
                $builder->like('tag1', $tag);   // tag 컬럼에 searchTag를 포함하는 데이터
            }
        }
        if (!empty($searchTag2)) {
            foreach ($searchTag2 as $tag) {
                $builder->like('tag2', $tag);   // tag 컬럼에 searchTag를 포함하는 데이터
            }
        }
        if (!empty($searchTag3)) {
            foreach ($searchTag3 as $tag) {
                $builder->like('tag3', $tag);   // tag 컬럼에 searchTag를 포함하는 데이터
            }
        }
        $builder->orderBy('trans_date', 'DESC');

        $res = $builder->get()->getResultArray();

        return $this->response->setJSON([
                'success' => true,
                'data' => json_encode($res)
            ]);
    }
}