/**
 * EntryCreate automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

import com.fasterxml.jackson.annotation.JsonGetter;
import com.fasterxml.jackson.annotation.JsonSetter;
import java.time.LocalDateTime;
public class EntryCreate {
    private int id;
    private int userId;
    private String title;
    private LocalDateTime date;
    @JsonSetter("id")
    public void setId(int id) {
        this.id = id;
    }
    @JsonGetter("id")
    public int getId() {
        return this.id;
    }
    @JsonSetter("userId")
    public void setUserId(int userId) {
        this.userId = userId;
    }
    @JsonGetter("userId")
    public int getUserId() {
        return this.userId;
    }
    @JsonSetter("title")
    public void setTitle(String title) {
        this.title = title;
    }
    @JsonGetter("title")
    public String getTitle() {
        return this.title;
    }
    @JsonSetter("date")
    public void setDate(LocalDateTime date) {
        this.date = date;
    }
    @JsonGetter("date")
    public LocalDateTime getDate() {
        return this.date;
    }
}
