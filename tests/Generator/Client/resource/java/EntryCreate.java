/**
 * EntryCreate generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import java.time.LocalDateTime;
public class EntryCreate {
    private int id;
    private int userId;
    private String title;
    private LocalDateTime date;
    public void setId(int id) {
        this.id = id;
    }
    public int getId() {
        return this.id;
    }
    public void setUserId(int userId) {
        this.userId = userId;
    }
    public int getUserId() {
        return this.userId;
    }
    public void setTitle(String title) {
        this.title = title;
    }
    public String getTitle() {
        return this.title;
    }
    public void setDate(LocalDateTime date) {
        this.date = date;
    }
    public LocalDateTime getDate() {
        return this.date;
    }
}
