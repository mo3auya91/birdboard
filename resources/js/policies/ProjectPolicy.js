export default class ProjectPolicy {
    /**
     * Determine whether the user can view any models.
     *
     * @param  {object}  user
     * @return mixed
     */
    viewAny(user) {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  {object}  user
     * @param  {object}  project
     * @return {mixed}
     */
    view(user, project) {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  {object}  user
     * @return {mixed}
     */
    create(user) {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  {object}  user
     * @param  {object}  project
     * @return {boolean}
     */
    update(user, project) {
        //return $user->is($project->owner) || $project->members->contains($user);
        return parseInt(project.owner_id) === parseInt(user.id) || project.members.filter(member => {
            return parseInt(member.id) === parseInt(user.id)
        }).length
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  {object}  user
     * @param  {object}  project
     * @return {boolean}
     */
    manage(user, project) {
        return parseInt(project.owner_id) === parseInt(user.id)
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  {object}  user
     * @param  {object}  project
     * @return {mixed}
     */
    restore(user, project) {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  {object}  user
     * @param  {object}  project
     * @return {mixed}
     */
    delete(user, project) {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  {object}  user
     * @param  {object}  project
     * @return {mixed}
     */
    forceDelete(user, project) {
        //
    }
}
