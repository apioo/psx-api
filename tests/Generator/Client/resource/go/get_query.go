
// GetQuery generated on 0000-00-00
// @see https://sdkgen.app


import "time"
type GetQuery struct {
    StartIndex int `json:"startIndex"`
    Float float64 `json:"float"`
    Boolean bool `json:"boolean"`
    Date time.Time `json:"date"`
    Datetime time.Time `json:"datetime"`
}
