/**
 * PetsResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {AxiosInstance, AxiosPromise, Method} from "axios";
import {ResourceAbstract} from "sdkgen-client"
import {PetsGetQuery} from "./PetsGetQuery";
import {Pets} from "./Pets";
import {Pet} from "./Pet";

export default class PetsResource extends ResourceAbstract {
    private url: string;


    public constructor(baseUrl: string, httpClient?: AxiosInstance) {
        super(baseUrl, httpClient);


        this.url = baseUrl + "/pets";
    }

    /**
     * List all pets
     *
     * @param {PetsGetQuery} query
     * @returns {AxiosPromise<Pets>}
     */
    public listPets(query?: PetsGetQuery): AxiosPromise<Pets> {
        let params = {
            method: <Method> "GET",
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
    public createPets(data?: Pet) {
        let params = {
            method: <Method> "POST",
        };

        return this.httpClient.post(this.url, data, params);
    }

}

/**
 * Pet generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface Pet {
    id: number
    name: string
    tag?: string
}

/**
 * Pets generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {Pet} from "./Pet";
export interface Pets {
    pets?: Array<Pet>
}

/**
 * Error generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface Error {
    code: number
    message: string
}

/**
 * PetsGetQuery generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface PetsGetQuery {
    limit?: number
}

/**
 * PetsPetIdGetQuery generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface PetsPetIdGetQuery {
    petId?: string
}

/**
 * Client generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ClientAbstract, TokenStoreInterface} from "sdkgen-client"

import PetsResource from "./PetsResource";

export default class Client extends ClientAbstract {
    public constructor(baseUrl: string, , tokenStore: TokenStoreInterface|null = null) {
        super(baseUrl, null, tokenStore);
    }

    /**
     * Endpoint: /pets
     *
     * @returns PetsResource
     */
    public getPets(): PetsResource
    {
        return new PetsResource(
            this.baseUrl,
            this.newHttpClient()
        );
    }

}
