### setup
- git clone [project]
- cd to [project dir]
- make create-env
- review (optionally fill in) ./docker/.env parameters
- make up
- make init

#### update
- make rebuild

#### develop
- make console
- make fixtures

### example requests
- create tag
```bash
curl --request POST 'http://project.url/api/v1/tags' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--data-raw '{
    "name":"some tag"
}'
```
```json
{
    "id": "018bf269-1796-ad6b-8a8d-5c13c5fbb0b6",
    "name": "some tag"
}
```
- update tag
```bash
curl --request PUT 'http://project.url/api/v1/tags/018bf269-1796-ad6b-8a8d-5c13c5fbb0b6' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--data-raw '{
    "name":"tag name"
}'
```
```json
{
    "id": "018bf269-1796-ad6b-8a8d-5c13c5fbb0b6",
    "name": "tag name"
}
```
- list tags
```bash
curl --request GET 'http://project.url/api/v1/tags?page=1&per_page=20' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json'
```
```json
[
    {
        "id": "018bfc69-ec7b-9941-f553-8c1132eb7f74",
        "name": "blue"
    },
    {
        "id": "018bfc69-fc3d-cf32-1d0a-499e345c0834",
        "name": "green"
    },
    {
        "id": "018bfc6a-07ca-44c5-fb48-9c53f60bd530",
        "name": "red one"
    }
]
```
- create article
```bash
curl --request POST 'http://project.url/api/v1/articles' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--data-raw '{
    "title":"article title",
    "tags": ["018bf269-1796-ad6b-8a8d-5c13c5fbb0b6"]
}'
```
```json
{
    "id": "018bfaef-b7bf-3945-9ccf-35d612f56580",
    "title": "article title",
    "tags": [
        {
            "id": "018bf269-1796-ad6b-8a8d-5c13c5fbb0b6",
            "name": "tag name",
            "created_at": "2023-11-23T15:48:16+00:00"
        }
    ]
}
```
- update article
```bash
curl --request PATCH 'http://project.url/api/v1/articles/018bfc6b-c2b0-72b9-bc48-434d9e466ce2' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--data-raw '{
    "title":"New title",
    "tags":["018bfc69-ec7b-9941-f553-8c1132eb7f74", "018bfc69-fc3d-cf32-1d0a-499e345c0834"]
}'
```
```json
{
    "id": "018bfaef-b7bf-3945-9ccf-35d612f56580",
    "title": "New title",
    "tags": [
        {
            "id": "018bfc69-ec7b-9941-f553-8c1132eb7f74",
            "name": "blue",
            "created_at": "2023-11-23T15:05:22+00:00"
        },
        {
            "id": "018bfc69-fc3d-cf32-1d0a-499e345c0834",
            "name": "green",
            "created_at": "2023-11-23T15:05:22+00:00"
        }
    ]
}
```
- list articles
```bash
curl --request GET 'http://project.url/api/v1/articles?page=1&per_page=20&tags[]=018bfc69-ec7b-9941-f553-8c1132eb7f74&tags[]=018bfc69-fc3d-cf32-1d0a-499e345c0834' \
--header 'Content-Type: application/json'
```
```json
[
    {
        "id": "018bfc6b-a40d-4a6e-2fe1-e7a9851e146d",
        "title": "Article five",
        "tags": [
            {
                "id": "018bfc69-ec7b-9941-f553-8c1132eb7f74",
                "name": "blue",
                "created_at": "2023-11-23T15:43:14+00:00"
            },
            {
                "id": "018bfc69-fc3d-cf32-1d0a-499e345c0834",
                "name": "green",
                "created_at": "2023-11-23T15:43:14+00:00"
            },
            {
                "id": "018bfc6a-07ca-44c5-fb48-9c53f60bd530",
                "name": "red one",
                "created_at": "2023-11-23T15:43:14+00:00"
            }
        ]
    },
    {
        "id": "018bfc6b-c2b0-72b9-bc48-434d9e466ce2",
        "title": "New title",
        "tags": [
            {
                "id": "018bfc69-ec7b-9941-f553-8c1132eb7f74",
                "name": "blue",
                "created_at": "2023-11-23T15:05:22+00:00"
            },
            {
                "id": "018bfc69-fc3d-cf32-1d0a-499e345c0834",
                "name": "green",
                "created_at": "2023-11-23T15:05:22+00:00"
            }
        ]
    }
]
```
- remove article
```bash
curl --request DELETE 'http://project.url/api/v1/articles/018bfaef-b7bf-3945-9ccf-35d612f56580' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json'
```
