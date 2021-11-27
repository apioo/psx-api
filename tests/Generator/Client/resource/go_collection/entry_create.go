
// EntryCreate generated on 0000-00-00
// @see https://sdkgen.app


import "time"
type EntryCreate struct {
    Id int `json:"id"`
    UserId int `json:"userId"`
    Title string `json:"title"`
    Date time.Time `json:"date"`
}
