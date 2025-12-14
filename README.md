# TripMate Backend (Vanilla PHP)

여행 일정(Trip / TripDay / ScheduleItem) 관리와 장소(Place) 저장·조회 기능을 제공하는  
REST API 기반 백엔드 서버입니다.

---

##  Members

<table>
  <tr>
    <td align="center">
      <img src="https://github.com/dgk99.png" width="100px;" alt="김민규"/><br />
      <sub><b>김민규</b></sub><br />
      팀장<br />
      <a href="https://github.com/dgk99" target="_blank">@dgk99</a>
    </td>
    <td align="center">
      <img src="https://github.com/jammmin02.png" width="100px;" alt="박정민"/><br />
      <sub><b>박정민</b></sub><br />
      팀원<br />
      <a href="https://github.com/jammmin02" target="_blank">@jammmin02</a>
    </td>
    <td align="center">
      <img src="https://github.com/dayeon2423004.png" width="100px;" alt="김다연"/><br />
      <sub><b>김다연</b></sub><br />
      팀원<br />
      <a href="https://github.com/dayeon2423004" target="_blank">@dayeon2423004</a>
    </td>
  </tr>
</table>

---

## System Architecture

<p align="center">
  <img src="[./docs/system-architecture.pn](https://github.com/gsc-lab/cs25-4-tripmate-backend/issues/120#issue-3727825025)" alt="System Architecture" width="800"/><br/>
  <sub>TripMate Backend System Architecture</sub>
</p>


**구성 흐름**

- User → Frontend(Web)
- Frontend → Backend API (Vanilla PHP)
- Backend → MySQL
- 외부 지도 API 결과 → Backend → DB(Place Upsert)

---

## Tech Stack

### Backend
- PHP (Vanilla)
- AltoRouter (Routing)
- JWT (Authentication)
- MySQL (InnoDB)

### Infrastructure
- REST API Architecture
- JSON 기반 통신

---

## Features

### 회원 관리
- 회원가입 / 로그인(JWT 발급)
- 로그아웃
- 내 정보 조회
- 회원 탈퇴

### 지역 관리
- 국가 코드 기반 지역 조회
- 지역 검색 기능
- 향후 국가 확장 고려한 구조 설계

### 여행(Trip) 관리
- Trip 생성 / 조회 / 수정 / 삭제
- TripDay 생성 / 조회 / 삭제
- TripDay 재배치(day_no 재정렬)

### 장소(Place) 관리
- 외부 지도 API 검색 결과 저장(Upsert)
- 저장된 장소 조회

### 일정(ScheduleItem) 관리
- ScheduleItem 생성 / 조회 / 수정 / 삭제
- 일정 아이템 순서 재배치(seq_no)
- 다중 재배치 및 교차 이동 처리

---

## Main API Endpoints

| Domain | Method | Endpoint | Description |
|---|---:|---|---|
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

---

### ScheduleItem 좌표 간 거리 계산
- ScheduleItem 간 이동 거리 계산을 위해 위도(lat) / 경도(lng) 좌표 기반 거리 계산 로직 적용
- Haversine(하버사인) 공식을 이용한 구면 거리 계산 방식 사용
- 두 좌표 간 직선 거리 계산을 통해 이동 거리 추정값 산출

**참고 자료**
- https://link2me.tistory.com/1831

---

### Place 외부 API (Google Maps Platform)
- Google Maps Platform의 **Places API (Web Service)** 기준으로 장소 검색 및 상세 정보 구조 설계
- 외부 API로부터 수집한 장소 정보를 내부 Place 테이블에 저장(Upsert)하여 활용

**공식 문서**
- https://developers.google.com/maps/documentation/places/web-service/overview?hl


