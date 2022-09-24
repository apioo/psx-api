const client = new Client(...)
client.getFooByNameAndType(name, type).listFoo(GetQuery): EntryCollection
client.getFooByNameAndType(name, type).createFoo(EntryCreate): EntryMessage
client.getFooByNameAndType(name, type).put(EntryUpdate): EntryMessage
client.getFooByNameAndType(name, type).delete(): EntryMessage
client.getFooByNameAndType(name, type).patch(EntryPatch): EntryMessage

interface Path {
    name: string
    type?: string
}

interface GetQuery {
    startIndex: number
    float?: number
    boolean?: boolean
    date?: string
    datetime?: string
}

interface EntryCollection {
    entry?: Array<Entry>
}

interface Entry {
    id?: number
    userId?: number
    title?: string
    date?: string
}

interface EntryCreate {
    id?: number
    userId?: number
    title: string
    date: string
}

interface EntryMessage {
    success?: boolean
    message?: string
}

interface EntryUpdate {
    id: number
    userId?: number
    title?: string
    date?: string
}

interface EntryDelete {
    id: number
    userId?: number
    title?: string
    date?: string
}

interface EntryPatch {
    id: number
    userId?: number
    title?: string
    date?: string
}
