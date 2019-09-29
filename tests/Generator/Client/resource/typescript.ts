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

        this.url = baseUrl + "";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     *
     * @param {GetQuery} query
     * @returns {AxiosPromise<Collection>}
     */
    public listFoo(query: GetQuery): AxiosPromise<Collection> {
        let params = {
            method: "GET",
            params: query,
        };

        return this.httpClient.get<Collection>(this.url, params);
    }

    /**
     * @param {Item} data
     * @returns {AxiosPromise<Message>}
     */
    public createFoo(data: Item): AxiosPromise<Message> {
        let params = {
            method: "POST",
        };

        return this.httpClient.post<Message>(this.url, data, params);
    }

    /**
     * @param {Item} data
     * @returns {AxiosPromise<Message>}
     */
    public put(data: Item): AxiosPromise<Message> {
        let params = {
            method: "PUT",
        };

        return this.httpClient.put<Message>(this.url, data, params);
    }

    /**
     * @returns {AxiosPromise<Message>}
     */
    public delete(): AxiosPromise<Message> {
        let params = {
            method: "DELETE",
        };

        return this.httpClient.delete(this.url, params);
    }

    /**
     * @param {Item} data
     * @returns {AxiosPromise<Message>}
     */
    public patch(data: Item): AxiosPromise<Message> {
        let params = {
            method: "PATCH",
        };

        return this.httpClient.patch<Message>(this.url, data, params);
    }

}

interface Endpoint {
    GetQuery?: GetQuery
    Collection?: Collection
    Item?: Item
    Message?: Message
}
interface GetQuery {
    startIndex: number
    float?: number
    boolean?: boolean
    date?: string
    datetime?: string
}
interface Collection {
    entry?: Array<Item>
}
interface Item {
    id: number
    userId?: number
    title?: string
    date?: string
}
interface Message {
    success?: boolean
    message?: string
}


