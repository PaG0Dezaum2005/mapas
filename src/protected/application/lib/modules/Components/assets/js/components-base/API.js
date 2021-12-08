const useEntitiesCache = Pinia.defineStore('entitiesCache', {
    state: () => {
        return {
            entities: {}
        }
    },

    actions: {
        store(entity) {
            this.entities[entity.cacheId] = entity;
        },

        remove(entity) {
            delete this.entities[entity.cacheId];
        },
        
        fetch(cacheId) {
            return this.entities[cacheId];
        }
    }
});

class API {
    constructor(objectType, options) {
        this.cache = useEntitiesCache();
        this.objectType = objectType;
        this.options = {
            cacheMode: 'force-cache', 
            ...options
        };
    }

    async GET(url, data, init) {
        const requestInit = {
            cache: this.options.cacheMode,
            ...init
        }

        return fetch(url, requestInit);
    }

    async PUT(url, data) {
        return fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
    }

    async PATCH(url, data) {
        return fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
    }

    async POST(url, data) {
        return fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
    }

    async DELETE(url, data) {
        return fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
    }

    async persistEntity(entity) {
        if (!entity.id) {
            let url = this.createUrl('index');
            return this.POST(url, entity.data())
            
        } else {
            let url = this.createUrl('single', [entity.id]);
            return this.PATCH(url, entity.data())
        }
    }

    async deleteEntity(entity) {
        if (entity.id) {
            return this.DELETE(entity.singleUrl);   
        }
    }

    async findOne(id) {
        let url = this.createApiUrl('findOne', {id: `EQ(${id})`, '@select': '*'});
        return this.GET(url).then(response => response.json().then(obj => {
            let entity = this.getEntityInstance(id);
            return entity.populate(obj);
        }));
    }

    async find(query) {
        let url = this.createApiUrl('find', query);
        return this.GET(url).then(response => response.json().then(objs => {
            let result = [];
            objs.forEach(element => {
                let entity = this.getEntityInstance(element.id);
                entity.populate(element);
                result.push(entity);
                entity.__lists.push(result);
            });

            return result;
        }));
    }

    createUrl(route, urlData) {
        let url = MapasCulturais.createUrl(this.objectType, route, urlData);
        return new URL(url);
    }

    createApiUrl(route, query) {
        let url = MapasCulturais.createUrl(`api/${this.objectType}/${route}`);
        let urlObject = new URL(url);
        for (var key in query) {
            urlObject.searchParams.append(key, query[key]);
        }

        return urlObject;
    }

    createCacheId(objectId) {
        return this.objectType + ':' + objectId;
    }

    getEntityInstance(objectId) {
        const cacheId = this.createCacheId(objectId);
        let entity = this.cache.fetch(cacheId);
        if (entity) {
            return entity;
        } else {
            entity = new Entity(this.objectType, objectId); 
            this.cache.store(entity);
            return entity;
        }
    }

    getEntityDescription(filter) {
        const description = MapasCulturais.EntitiesDescription[this.objectType];
                
        let result = {};

        function filteredBy(f) {
            let filters = filter.split(',');
            return filters.indexOf(f) > -1;
        }

        for (var key in description) {
            if(key.substr(0,2) === '__') {
                continue;
            }

            let desc = description[key];
            let ok = true
            
            if (filter) {
                if (filteredBy('private') && desc.private !== true) {
                    ok = false;
                }

                if (filteredBy('public') && desc.private) {
                    ok = false
                }

                if (filteredBy('metadata') && !desc.isMetadata) {
                    ok = false
                } else if(filteredBy('!metadata') && desc.isMetadata) {
                    ok = false
                }

                if (filteredBy('relations') && !desc.isEntityRelation) {
                    ok = false
                } else if(filteredBy('!relations') && desc.isEntityRelation) {
                    ok = false
                }
            }
                
            if (ok) {
                key = desc['@select'] || key;

                if (desc.isEntityRelation && key[0] == '_'){
                    key = key.substr(1);
                }
                
                result[key] = desc;
            }
        }

        return result;
    }
}