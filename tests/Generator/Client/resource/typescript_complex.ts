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
     * @param {Item | Message} data
     * @returns {AxiosPromise<Item | Message>}
     */
    public post(data: Item | Message): AxiosPromise<Item | Message> {
        let params = {
            method: "POST",
            headers: {
                'Authorization': 'Bearer ' + this.token
            },
        };

        return this.httpClient.post<Item | Message>(this.url, data, params);
    }

}


/**
 * FooNameTypeResourceSchema generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface FooNameTypeResourceSchema {
    EntryOrMessage?: Item | Message
}

/**
 * Item generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

interface Item {
    id?: number
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
