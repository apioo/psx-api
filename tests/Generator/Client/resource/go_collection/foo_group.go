
// FooGroup automatically generated by SDKgen please do not edit this file manually
// @see https://sdkgen.app



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type FooGroup struct {
    BaseUrl string
    Token   string
}

// Endpoint: /foo
func (client Client) getFoo() FooResource {
    r := FooResource {
        BaseUrl: client.BaseUrl,
        Token: client.Token
    }
    return r
}

func NewClient(baseUrl string, token string) Client {
    c := Client {
        BaseUrl: baseUrl,
        Token: token
    }
    return c
}
