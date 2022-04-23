
// FooGroup generated on 0000-00-00
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
