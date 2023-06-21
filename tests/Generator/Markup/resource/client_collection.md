const client = new Client()
client.foo().get(): EntryCollection
client.foo().create(payload: EntryCreate): EntryMessage throws EntryMessage
client.bar().get(foo: string): EntryCollection
client.bar().create(payload: EntryCreate): EntryMessage
client.baz().get(year: string): EntryCollection
client.baz().create(payload: EntryCreate): EntryMessage


interface EntryCollection {
    entry?: Array<Entry>
}

interface Entry {
    id?: number
    userId?: number
    title?: string
    date?: string
}

interface EntryMessage {
    success?: boolean
    message?: string
}

interface EntryCreate {
    id?: number
    userId?: number
    title: string
    date: string
}
