<?php
// namespace 작성
namespace Tripmate\Backend\Common\Utils;

// Date 클래스 정의
class Date
{
    // 'YYYY-MM-DD' 형식 검증 메서드
    public static function isValidDateYmd(string $ymd): bool{
        // 길이가 10자리가 아니면 탈락
        if (strlen($ymd) !== 10) return false;
        // 구분자 '-'로 분리
        [$y, $m, $d] = explode('-', $ymd) + [null, null, null];
        // 연,월,일이 모두 숫자인지 확인
        if (!ctype_digit($y ?? '') || !ctype_digit($m ?? '') || !ctype_digit($d ?? '')) {
            return false;
        }
        // 실제로 존재하는 날짜인지 체크
        return checkdate((int)$m, (int)$d, (int)$y);
    }
}
