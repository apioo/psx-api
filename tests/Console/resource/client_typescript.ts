import Axios, {AxiosInstance, AxiosPromise} from "axios";

export default class Resource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private fooId: string;

    public constructor(fooId: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.fooId = fooId;

        this.url = baseUrl + "/foo";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * A long **Test** description
     */
    public doGet(query: GetQuery): AxiosPromise<Song> {
        let params = {
            method: "GET",
            params: query,
        };

        return this.httpClient.get<Song>(this.url, params);
    }

}

interface Endpoint {
    GetQuery?: GetQuery
    Song?: Song
}
interface GetQuery {
    foo?: string
    bar: string
    baz?: string
    boz?: string
    integer?: number
    number?: number
    date?: string
    boolean?: boolean
    string?: string
}
interface Song {
    title: string
    artist: string
    length?: number
    ratings?: Array<Rating>
}
interface Rating {
    author?: string
    rating?: number
    text?: string
}


