/**
 * BarYear09Resource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";


export default class BarYear09Resource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private year: string;

    public constructor(year: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.year = year;

        this.url = baseUrl + "/bar/"+year+"";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     *
     * @returns {AxiosPromise<Collection>}
     */
    public get(): AxiosPromise<Collection> {
        let params = {
            method: "GET",
        };

        return this.httpClient.get<Collection>(this.url, params);
    }

    /**
     * @param {ItemCreate} data
     * @returns {AxiosPromise<Message>}
     */
    public post(data: ItemCreate): AxiosPromise<Message> {
        let params = {
            method: "POST",
        };

        return this.httpClient.post<Message>(this.url, data, params);
    }

}

