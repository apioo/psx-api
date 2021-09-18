
// EntryPatch generated on 0000-00-00
// {@link https://github.com/apioo}


import "time"
type EntryPatch struct {
    Id int `json:"id"`
    UserId int `json:"userId"`
    Title string `json:"title"`
    Date time.Time `json:"date"`
}
