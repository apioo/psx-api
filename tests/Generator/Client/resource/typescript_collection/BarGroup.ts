/**
 * BarGroup generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ResourceAbstract} from "sdkgen-client"
import BarByFooResource from "./BarByFooResource";
import BarByYearResource from "./BarByYearResource";

export default class BarGroup extends ResourceAbstract {
    /**
     * Endpoint: /bar/:foo
     *
     * @returns BarByFooResource
     */
    public getBarByFoo(foo: string): BarByFooResource
    {
        return new BarByFooResource(
            foo,
            this.baseUrl,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/$year<[0-9]+>
     *
     * @returns BarByYearResource
     */
    public getBarByYear(year: string): BarByYearResource
    {
        return new BarByYearResource(
            year,
            this.baseUrl,
            this.httpClient
        );
    }

}
