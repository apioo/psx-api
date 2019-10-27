/**
 * FooNameTypeResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";


export default class FooNameTypeResource {
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


/**
 * FooNameTypeResourceSchema generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface FooNameTypeResourceSchema {
    GetQuery?: GetQuery
    Collection?: Collection
    Item?: Item
    Message?: Message
}

/**
 * GetQuery generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface GetQuery {
    startIndex: number
    float?: number
    boolean?: boolean
    date?: string
    datetime?: string
}

/**
 * Collection generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface Collection {
    entry?: Array<Item>
}

/**
 * Item generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface Item {
    id: number
    userId?: number
    title?: string
    date?: string
}

/**
 * Message generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface Message {
    success?: boolean
    message?: string
}
