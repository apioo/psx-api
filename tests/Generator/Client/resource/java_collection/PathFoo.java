/**
 * PathFoo automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

import com.fasterxml.jackson.annotation.JsonGetter;
import com.fasterxml.jackson.annotation.JsonSetter;
public class PathFoo {
    private String foo;
    @JsonSetter("foo")
    public void setFoo(String foo) {
        this.foo = foo;
    }
    @JsonGetter("foo")
    public String getFoo() {
        return this.foo;
    }
}
