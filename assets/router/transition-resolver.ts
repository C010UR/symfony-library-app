import type { RouteLocationNormalized } from 'vue-router';

function resolveTransition(to: RouteLocationNormalized, from: RouteLocationNormalized): string {
  if (from.meta.transitionType === 'Auth' && to.meta.transitionType === 'Auth') {
    let _from: number, _to: number;

    if (typeof from.meta.transitionLevel === 'number') {
      _from = from.meta.transitionLevel;
    } else {
      _from = 0;
    }

    if (typeof to.meta.transitionLevel === 'number') {
      _to = to.meta.transitionLevel;
    } else {
      _to = 0;
    }

    return _from - _to > 0 ? 'slide-to-bottom' : 'slide-to-top';
  }

  return '';
}

export { resolveTransition };
