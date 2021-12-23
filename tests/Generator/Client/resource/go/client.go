
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

// Endpoint: /foo/:name/:type
func (client Client) getFooByNameAndType(string name, string type) FooByNameAndTypeResource {
    r := FooByNameAndTypeResource {
        Name: name,
        Type: type,
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
