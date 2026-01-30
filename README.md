# TripMate Backend (Vanilla PHP)

旅行日程（Trip / TripDay / ScheduleItem）および場所（Place）を管理する  
**REST API ベースのバックエンドサーバー**です。

---

## Members

<table>
  <tr>
    <td align="center">
      <img src="https://github.com/dgk99.png" width="100"/><br/>
      <b>キム・ミンギュ</b><br/>
      チームリーダー<br/>
      <a href="https://github.com/dgk99">@dgk99</a>
    </td>
    <td align="center">
      <img src="https://github.com/jammmin02.png" width="100"/><br/>
      <b>パク・ジョンミン</b><br/>
      メンバー<br/>
      <a href="https://github.com/jammmin02">@jammmin02</a>
    </td>
    <td align="center">
      <img src="https://github.com/dayeon2423004.png" width="100"/><br/>
      <b>キム・ダヨン</b><br/>
      メンバー<br/>
      <a href="https://github.com/dayeon2423004">@dayeon2423004</a>
    </td>
  </tr>
</table>

---

## System Architecture

<p align="center">
  <img src="https://private-user-images.githubusercontent.com/162419902/526323393-9e8a2c40-6b16-4b59-9fe3-cee26e3dfa94.png" width="800"/>
</p>

---

## Tech Stack & Skills

### Frontend
<div align="center">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black"/>
  <img src="https://img.shields.io/badge/React-61DAFB?style=for-the-badge&logo=react&logoColor=black"/>
  <img src="https://img.shields.io/badge/MUI-007FFF?style=for-the-badge&logo=mui&logoColor=white"/>
  <img src="https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=node.js&logoColor=white"/>
</div>

- SPA ベースの UI 構成（React）
- REST API（JSON）連携
- コンポーネント単位の UI 設計

---

### Backend
<div align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Laravel-EF3B2D?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/REST%20API-005571?style=for-the-badge"/>
  <img src="https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white"/>
</div>

- Vanilla PHP による REST API サーバー実装
- Laravel フレームワークを活用した構造化されたバックエンド設計
- JWT ベースの認証処理

---

### Database
<div align="center">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
</div>

- リレーショナルデータモデリング
- トランザクションを用いた順序再配置ロジック
- Unique 制約を考慮したデータ整合性管理

---

### Infrastructure & DevOps
<div align="center">
  <img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white"/>
  <img src="https://img.shields.io/badge/Nginx-009639?style=for-the-badge&logo=nginx&logoColor=white"/>
</div>

- Docker コンテナベースの開発環境構築
- Frontend / Backend / DB の分離運用
- REST API 通信構造の設計

---

### Collaboration & Tools
<div align="center">
  <img src="https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white"/>
  <img src="https://img.shields.io/badge/VS%20Code-007ACC?style=for-the-badge&logo=visualstudiocode&logoColor=white"/>
</div>

- GitHub を活用した協業およびバージョン管理
- PR テンプレートを用いたコードレビュー

---

## Core Features

### 1. ユーザー管理
- 会員登録 / ログイン（JWT 発行）
- ログアウト
- マイページ情報取得
- 会員退会

### 2. 地域管理
- 国コードに基づく地域情報取得
- 地域検索機能
- 将来的な国追加を考慮した設計

### 3. 旅行（Trip）管理
- Trip 作成 / 取得 / 更新 / 削除
- TripDay 作成 / 削除
- TripDay 並び替え（day_no 再整理）

### 4. 場所（Place）管理
- 外部地図 API の検索結果を保存（Upsert）
- 保存済み場所情報の取得

### 5. 日程（ScheduleItem）管理
- 日程の作成 / 取得 / 更新 / 削除
- 日程順序の再配置（seq_no）
- 複数同時再配置およびクロス移動対応

---

## Main API Endpoints

| Domain | Method | Endpoint | Description |
|------|--------|----------|-------------|
| Auth | POST | /api/v1/auth/register | 会員登録 |
| Auth | POST | /api/v1/auth/login | ログイン |
| Auth | POST | /api/v1/auth/logout | ログアウト |
| User | GET | /api/v1/me | ユーザー情報取得 |
| User | DELETE | /api/v1/me | 会員退会 |
| Region | GET | /api/v1/regions | 地域取得 / 検索 |
| Trip | GET / POST | /api/v1/trips | Trip 一覧 / 作成 |
| Trip | GET / PUT / DELETE | /api/v1/trips/{trip_id} | Trip 詳細 |
| TripDay | POST | /api/v1/trips/{trip_id}/days | TripDay 作成 |
| TripDay | PUT | /api/v1/trips/{trip_id}/days/reorder | TripDay 並び替え |
| Place | POST | /api/v1/places/upsert | 場所 Upsert |
| Item | POST | /api/v1/trips/{trip_id}/days/{day_no}/items | 日程作成 |
| Item | PUT | /api/v1/trips/{trip_id}/days/{day_no}/items/reorder | 日程並び替え |

---

## ScheduleItem 距離計算

- 緯度（lat）/ 経度（lng）に基づく距離計算
- Haversine（ハーバサイン）公式を使用
- 日程間の移動距離の推定値を算出

**Reference**  
- https://link2me.tistory.com/1831

---

## External API – Google Maps Platform

- Places API（Web Service）を基準に場所情報を取得
- 外部の場所データを内部 Place テーブルに保存し再利用

**Official Docs**  
- https://developers.google.com/maps/documentation/places/web-service/overview?hl=ja
