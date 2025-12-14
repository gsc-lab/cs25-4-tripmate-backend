# TripMate Backend (Vanilla PHP)

여행 일정(Trip / TripDay / ScheduleItem)과 장소(Place)를 관리하는  
**REST API 기반 백엔드 서버**입니다.

## Members

<table>
  <tr>
    <td align="center">
      <img src="https://github.com/dgk99.png" width="100"/><br/>
      <b>김민규</b><br/>
      팀장<br/>
      <a href="https://github.com/dgk99">@dgk99</a>
    </td>
    <td align="center">
      <img src="https://github.com/jammmin02.png" width="100"/><br/>
      <b>박정민</b><br/>
      팀원<br/>
      <a href="https://github.com/jammmin02">@jammmin02</a>
    </td>
    <td align="center">
      <img src="https://github.com/dayeon2423004.png" width="100"/><br/>
      <b>김다연</b><br/>
      팀원<br/>
      <a href="https://github.com/dayeon2423004">@dayeon2423004</a>
    </td>
  </tr>
</table>


## System Architecture

<p align="center">
  <img src="https://private-user-images.githubusercontent.com/162419902/526323393-9e8a2c40-6b16-4b59-9fe3-cee26e3dfa94.png?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3NjU3MzgxOTYsIm5iZiI6MTc2NTczNzg5NiwicGF0aCI6Ii8xNjI0MTk5MDIvNTI2MzIzMzkzLTllOGEyYzQwLTZiMTYtNGI1OS05ZmUzLWNlZTI2ZTNkZmE5NC5wbmc_WC1BbXotQWxnb3JpdGhtPUFXUzQtSE1BQy1TSEEyNTYmWC1BbXotQ3JlZGVudGlhbD1BS0lBVkNPRFlMU0E1M1BRSzRaQSUyRjIwMjUxMjE0JTJGdXMtZWFzdC0xJTJGczMlMkZhd3M0X3JlcXVlc3QmWC1BbXotRGF0ZT0yMDI1MTIxNFQxODQ0NTZaJlgtQW16LUV4cGlyZXM9MzAwJlgtQW16LVNpZ25hdHVyZT0xYjNiMjYyNGYyMDgyOWY1MWY2NzRkNGYxMzk5NDQ0MTFmNWMxNzFiZmE3MDhjZTQyMzhjNjA1ODgxNjQ3YTk3JlgtQW16LVNpZ25lZEhlYWRlcnM9aG9zdCJ9.-XwblEYy-Fh1kH4rt8ZCSDcaawsVnWtvMbn35bb50OI" width="800"/>
</p>

## Tech Stack & Skills

### Frontend
<div align="center">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black"/>
  <img src="https://img.shields.io/badge/React-61DAFB?style=for-the-badge&logo=react&logoColor=black"/>
  <img src="https://img.shields.io/badge/MUI-007FFF?style=for-the-badge&logo=mui&logoColor=white"/>
  <img src="https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=node.js&logoColor=white"/>
</div>

- SPA 기반 UI 구성 (React)
- REST API(JSON) 연동
- 컴포넌트 단위 UI 설계

### Backend
<div align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Laravel-EF3B2D?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/REST%20API-005571?style=for-the-badge"/>
  <img src="https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white"/>
</div>

- Vanilla PHP 기반 REST API 서버 구현
- Laravel 프레임워크 활용한 구조화된 백엔드 설계
- JWT 기반 인증 처리


### Database
<div align="center">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
</div>

- 관계형 데이터 모델링
- 트랜잭션 기반 순서 재배치 로직
- Unique 제약 조건을 고려한 데이터 무결성 관리


### Infrastructure & DevOps
<div align="center">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white"/>
  <img src="https://img.shields.io/badge/Nginx-009639?style=for-the-badge&logo=nginx&logoColor=white"/>
</div>

- Docker 컨테이너 기반 개발 환경 구성
- Frontend / Backend / DB 분리 운영
- REST API 통신 구조 설계


### Collaboration & Tools
<div align="center">
  <img src="https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white"/>
  <img src="https://img.shields.io/badge/VS%20Code-007ACC?style=for-the-badge&logo=visualstudiocode&logoColor=white"/>
</div>

- GitHub 기반 협업 및 버전 관리
- PR 템플릿 활용한 코드 리뷰



## Core Features

### 1. 회원 관리
- 회원가입 / 로그인 (JWT 발급)
- 로그아웃
- 내 정보 조회
- 회원 탈퇴

### 2. 지역 관리
- 국가 코드 기반 지역 조회
- 지역 검색 지원
- 국가 확장 고려한 구조 설계

### 3. 여행(Trip) 관리
- Trip 생성 / 조회 / 수정 / 삭제
- TripDay 생성 / 삭제
- TripDay 재배치 (day_no 재정렬)

### 4. 장소(Place) 관리
- 외부 지도 API 검색 결과 저장 (Upsert)
- 저장된 장소 조회

### 5. 일정(ScheduleItem) 관리
- 일정 생성 / 조회 / 수정 / 삭제
- 일정 순서 재배치 (seq_no)
- 다중 재배치 및 교차 이동 처리


##  Main API Endpoints

| Domain | Method | Endpoint | Description |
|------|--------|----------|-------------|
| Auth | POST | /api/v1/auth/register | 회원가입 |
| Auth | POST | /api/v1/auth/login | 로그인 |
| Auth | POST | /api/v1/auth/logout | 로그아웃 |
| User | GET | /api/v1/me | 내 정보 조회 |
| User | DELETE | /api/v1/me | 회원 탈퇴 |
| Region | GET | /api/v1/regions | 지역 조회 / 검색 |
| Trip | GET / POST | /api/v1/trips | Trip 목록 / 생성 |
| Trip | GET / PUT / DELETE | /api/v1/trips/{trip_id} | Trip 단건 |
| TripDay | POST | /api/v1/trips/{trip_id}/days | TripDay 생성 |
| TripDay | PUT | /api/v1/trips/{trip_id}/days/reorder | TripDay 재배치 |
| Place | POST | /api/v1/places/upsert | 장소 Upsert |
| Item | POST | /api/v1/trips/{trip_id}/days/{day_no}/items | 일정 생성 |
| Item | PUT | /api/v1/trips/{trip_id}/days/{day_no}/items/reorder | 일정 재배치 |

## ScheduleItem 거리 계산

- 위도(lat) / 경도(lng) 기반 거리 계산
- Haversine(하버사인) 공식 사용
- 일정 간 이동 거리 추정값 산출

**Reference**
- https://link2me.tistory.com/1831


## External API – Google Maps Platform

- Places API (Web Service) 기준으로 장소 정보 수집
- 외부 장소 데이터를 내부 Place 테이블에 저장하여 재사용

**Official Docs**
- https://developers.google.com/maps/documentation/places/web-service/overview?hl=ko

