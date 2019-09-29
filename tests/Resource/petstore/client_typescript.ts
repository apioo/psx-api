import Axios, {AxiosInstance, AxiosPromise} from "axios";

export default class Resource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;


    public constructor(baseUrl: string, token: string, httpClient?: AxiosInstance) {

        this.url = baseUrl + "";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * List all pets
     *
     * @param {GetQuery} query
     * @returns {AxiosPromise<Pets>}
     */
    public listPets(query: GetQuery): AxiosPromise<Pets> {
        let params = {
            method: "GET",
            params: query,
        };

        return this.httpClient.get<Pets>(this.url, params);
    }

    /**
     * Create a pet
     *
     * @param {Pet} data
     * @returns {AxiosPromise<void>}
     */
    public createPets(data: Pet) {
        let params = {
            method: "POST",
        };

        return this.httpClient.post(this.url, data, params);
    }

}

interface Endpoint {
    GetQuery?: GetQuery
    Pets?: Pets
    Pet?: Pet
}
interface GetQuery {
    limit?: number
}
interface Pets {
    pets?: Array<Pet>
}
interface Pet {
    id: number
    name: string
    tag?: string
}


