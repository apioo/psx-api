namespace Foo {
    export interface Endpoint {
        Collection?: Collection
        Item?: Item
        Message?: Message
    }
    export interface Collection {
        entry?: Array<Item>
    }
    export interface Item {
        id?: number
        userId?: number
        title: string
        date: string
    }
    export interface Message {
        success?: boolean
        message?: string
    }
    
}

namespace BarFoo {
    export interface Endpoint {
        Collection?: Collection
        Item?: Item
        Message?: Message
    }
    export interface Collection {
        entry?: Array<Item>
    }
    export interface Item {
        id?: number
        userId?: number
        title: string
        date: string
    }
    export interface Message {
        success?: boolean
        message?: string
    }
    
}

namespace BarYear09 {
    export interface Endpoint {
        Collection?: Collection
        Item?: Item
        Message?: Message
    }
    export interface Collection {
        entry?: Array<Item>
    }
    export interface Item {
        id?: number
        userId?: number
        title: string
        date: string
    }
    export interface Message {
        success?: boolean
        message?: string
    }
    
}

