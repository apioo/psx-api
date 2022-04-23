
// BarGroup generated on 0000-00-00
// @see https://sdkgen.app



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type BarGroup struct {
    BaseUrl string
    Token   string
}

// Endpoint: /bar/:foo
func (client Client) getBarByFoo(string foo) BarByFooResource {
    r := BarByFooResource {
        Foo: foo,
        BaseUrl: client.BaseUrl,
        Token: client.Token
    }
    return r
}
// Endpoint: /bar/$year<[0-9]+>
func (client Client) getBarByYear(string year) BarByYearResource {
    r := BarByYearResource {
        Year: year,
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
