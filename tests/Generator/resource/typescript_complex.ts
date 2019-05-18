import Axios, {AxiosInstance, AxiosPromise} from "axios";

export default class Resource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private name: string;
    private type: string;

    public constructor(name: string, type: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     */
    public get(): AxiosPromise<Collection> {
        let params = {
            method: "GET",
            headers: {
                'Authorization': 'Bearer ' + this.token
            },
        };

        return this.httpClient.get<Collection>(this.url, params);
    }

}

interface Endpoint {
    Collection?: Collection
}
interface Collection {
    entry?: Array<Item>
}
interface Item {
    id?: number
    userId?: number
    title?: string
    date?: string
}


