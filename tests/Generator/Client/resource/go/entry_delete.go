
// entry_delete automatically generated by SDKgen please do not edit this file manually
// @see https://sdkgen.app


import "time"
type EntryDelete struct {
    Id int `json:"id"`
    UserId int `json:"userId"`
    Title string `json:"title"`
    Date time.Time `json:"date"`
}
