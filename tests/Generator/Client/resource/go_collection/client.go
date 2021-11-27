
// Client generated on 0000-00-00
// @see https://sdkgen.app



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type Client struct {
    BaseUrl string
    Token   string
}

// Tag: foo
func (client Client) foo() FooGroup {
    r := FooGroup {
        BaseUrl: client.BaseUrl,
        Token: client.Token
    }
    return r
}
// Tag: bar
func (client Client) bar() BarGroup {
    r := BarGroup {
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
