import qs from 'qs';

export function useChangeQuery(path: string, query: object) {
  history.pushState({}, '', path + '?' + qs.stringify(query, { encode: false }));
}
