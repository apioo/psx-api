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
     * @returns {AxiosPromise<EntryCollection>}
     */
    public get(): AxiosPromise<EntryCollection> {
        let params = {
            method: "GET",
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public post(data: EntryCreate): AxiosPromise<EntryMessage> {
        let params = {
            method: "POST",
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

}

